<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ServiceRequest;
use App\Models\Service;
use App\Models\ServiceCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ServiceController extends Controller
{
    public function index(Request $request): View
    {
        $services = Service::query()
            ->with(['category', 'image'])
            ->when($request->boolean('trashed'), fn ($query) => $query->onlyTrashed())
            ->when($request->filled('q'), fn ($query) => $query->where('name', 'like', '%'.$request->string('q').'%'))
            ->when($request->filled('category'), fn ($query) => $query->where('service_category_id', $request->integer('category')))
            ->when($request->filled('status'), fn ($query) => $query->where('is_published', $request->input('status') === 'published'))
            ->ordered()
            ->paginate(20)
            ->withQueryString();

        return view('admin.services.index', [
            'services' => $services,
            'categories' => ServiceCategory::orderBy('sort_order')->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.services.form', $this->formData(new Service(['is_published' => true])));
    }

    public function store(ServiceRequest $request): RedirectResponse
    {
        $service = DB::transaction(function () use ($request): Service {
            $service = Service::create($request->payload());
            $service->syncGallery($request->input('gallery', []));

            return $service;
        });

        return redirect()->route('admin.services.edit', $service)->with('success', 'Serviço criado com sucesso.');
    }

    public function edit(Service $service): View
    {
        $service->load(['gallery', 'image', 'ogImage']);

        return view('admin.services.form', $this->formData($service));
    }

    public function update(ServiceRequest $request, Service $service): RedirectResponse
    {
        DB::transaction(function () use ($request, $service): void {
            $service->update($request->payload());
            $service->syncGallery($request->input('gallery', []));
        });

        return back()->with('success', 'Serviço actualizado com sucesso.');
    }

    public function destroy(Service $service): RedirectResponse
    {
        $service->delete();

        return redirect()->route('admin.services.index')->with('success', 'Serviço movido para a reciclagem.');
    }

    public function restore(int $service): RedirectResponse
    {
        Service::onlyTrashed()->findOrFail($service)->restore();

        return redirect()->route('admin.services.index')->with('success', 'Serviço restaurado.');
    }

    /**
     * @return array<string, mixed>
     */
    private function formData(Service $service): array
    {
        return [
            'service' => $service,
            'categories' => ServiceCategory::orderBy('sort_order')->orderBy('name')->get(),
        ];
    }
}
