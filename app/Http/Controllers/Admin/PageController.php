<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PageRequest;
use App\Models\Media;
use App\Models\Page;
use App\Support\PageTemplates;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class PageController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', Page::class);

        $pages = Page::query()
            ->when($request->boolean('trashed'), fn ($query) => $query->onlyTrashed())
            ->when($request->filled('q'), fn ($query) => $query->where('title', 'like', '%'.$request->string('q').'%'))
            ->orderByDesc('is_system')
            ->orderBy('sort_order')
            ->orderBy('title')
            ->paginate(30)
            ->withQueryString();

        return view('admin.pages.index', ['pages' => $pages, 'templates' => PageTemplates::labels()]);
    }

    public function create(): View
    {
        Gate::authorize('create', Page::class);

        return view('admin.pages.form', $this->formData(new Page(['template' => 'default', 'is_published' => true])));
    }

    public function store(PageRequest $request): RedirectResponse
    {
        $page = new Page($request->payload());
        $page->template = 'default';
        $page->save();

        return redirect()->route('admin.pages.edit', $page)->with('success', 'Página criada com sucesso.');
    }

    public function edit(Page $page): View
    {
        Gate::authorize('update', $page);

        $page->load(['heroImage', 'ogImage']);

        return view('admin.pages.form', $this->formData($page));
    }

    public function update(PageRequest $request, Page $page): RedirectResponse
    {
        $page->update($request->payload());

        return back()->with('success', 'Página actualizada com sucesso.');
    }

    public function destroy(Page $page): RedirectResponse
    {
        Gate::authorize('delete', $page);

        $page->delete();

        return redirect()->route('admin.pages.index')->with('success', 'Página movida para a reciclagem.');
    }

    public function restore(int $page): RedirectResponse
    {
        $page = Page::onlyTrashed()->findOrFail($page);
        Gate::authorize('restore', $page);

        $page->restore();

        return redirect()->route('admin.pages.index')->with('success', 'Página restaurada.');
    }

    /**
     * @return array<string, mixed>
     */
    private function formData(Page $page): array
    {
        $ids = [];
        $sections = $page->sections ?? [];
        array_walk_recursive($sections, function ($value) use (&$ids): void {
            if (is_int($value)) {
                $ids[] = $value;
            }
        });

        return [
            'page' => $page,
            'schema' => PageTemplates::sections($page->template ?? 'default'),
            'templates' => PageTemplates::labels(),
            'icons' => PageTemplates::icons(),
            'mediaPreviews' => Media::whereIn('id', $ids)->get()->mapWithKeys(fn (Media $media) => [$media->id => $media->toPickerArray()]),
        ];
    }
}
