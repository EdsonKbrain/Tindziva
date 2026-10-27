<?php

namespace App\Http\Controllers\Admin;

use App\Enums\MediaType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\MediaUploadRequest;
use App\Models\Media;
use App\Services\MediaService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class MediaController extends Controller
{
    public function __construct(private MediaService $mediaService) {}

    /**
     * Library screen, or JSON listing when requested by the media picker.
     */
    public function index(Request $request): View|JsonResponse
    {
        Gate::authorize('viewAny', Media::class);

        $request->validate([
            'type' => ['nullable', Rule::enum(MediaType::class)],
            'q' => ['nullable', 'string', 'max:100'],
        ]);

        $media = Media::query()
            ->search($request->input('q'))
            ->when($request->filled('type'), fn ($query) => $query->where('type', $request->input('type')))
            ->when($request->boolean('mine'), fn ($query) => $query->where('uploaded_by', $request->user()->id))
            ->latest()
            ->paginate($request->expectsJson() ? 24 : 36)
            ->withQueryString();

        if ($request->expectsJson()) {
            return response()->json([
                'data' => $media->getCollection()->map->toPickerArray(),
                'next_page_url' => $media->nextPageUrl(),
            ]);
        }

        $media->load('uploader');

        return view('admin.media.index', ['media' => $media, 'types' => MediaType::cases()]);
    }

    public function store(MediaUploadRequest $request): JsonResponse|RedirectResponse
    {
        $uploaded = collect($request->file('files'))->map(fn ($file) => $this->mediaService->store($file, [
            'alt_text' => $request->input('alt_text'),
            'caption' => $request->input('caption'),
        ]));

        if ($request->expectsJson()) {
            return response()->json(['data' => $uploaded->map->toPickerArray()], 201);
        }

        return back()->with('success', trans_choice('{1} Ficheiro carregado com sucesso.|[2,*] :count ficheiros carregados com sucesso.', $uploaded->count()));
    }

    public function update(Request $request, Media $media): JsonResponse|RedirectResponse
    {
        Gate::authorize('update', $media);

        $media->update($request->validate([
            'title' => ['nullable', 'string', 'max:190'],
            'alt_text' => ['nullable', 'string', 'max:255'],
            'caption' => ['nullable', 'string', 'max:500'],
        ]));

        if ($request->expectsJson()) {
            return response()->json(['data' => $media->toPickerArray()]);
        }

        return back()->with('success', 'Informação do ficheiro actualizada.');
    }

    public function destroy(Request $request, Media $media): JsonResponse|RedirectResponse
    {
        Gate::authorize('delete', $media);

        $this->mediaService->delete($media);

        if ($request->expectsJson()) {
            return response()->json(status: 204);
        }

        return back()->with('success', 'Ficheiro eliminado.');
    }
}
