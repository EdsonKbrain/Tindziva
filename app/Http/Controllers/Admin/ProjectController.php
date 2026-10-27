<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProjectRequest;
use App\Models\Client;
use App\Models\Project;
use App\Models\ProjectCategory;
use App\Models\Service;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ProjectController extends Controller
{
    public function index(Request $request): View
    {
        $projects = Project::query()
            ->with(['client', 'categories', 'coverImage'])
            ->when($request->boolean('trashed'), fn ($query) => $query->onlyTrashed())
            ->when($request->filled('q'), fn ($query) => $query->where('title', 'like', '%'.$request->string('q').'%'))
            ->when($request->filled('category'), fn ($query) => $query->whereHas('categories', fn ($query) => $query->whereKey($request->integer('category'))))
            ->when($request->filled('status'), fn ($query) => $query->where('is_published', $request->input('status') === 'published'))
            ->when($request->boolean('featured'), fn ($query) => $query->where('is_featured', true))
            ->latestFirst()
            ->paginate(20)
            ->withQueryString();

        return view('admin.projects.index', [
            'projects' => $projects,
            'categories' => ProjectCategory::orderBy('sort_order')->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.projects.form', $this->formData(new Project));
    }

    public function store(ProjectRequest $request): RedirectResponse
    {
        $project = DB::transaction(function () use ($request): Project {
            $project = Project::create($request->payload());
            $this->syncRelations($project, $request);

            return $project;
        });

        return redirect()->route('admin.projects.edit', $project)->with('success', 'Projecto criado com sucesso.');
    }

    public function edit(Project $project): View
    {
        $project->load(['gallery', 'coverImage', 'ogImage', 'categories', 'services']);

        return view('admin.projects.form', $this->formData($project));
    }

    public function update(ProjectRequest $request, Project $project): RedirectResponse
    {
        DB::transaction(function () use ($request, $project): void {
            $project->update($request->payload());
            $this->syncRelations($project, $request);
        });

        return back()->with('success', 'Projecto actualizado com sucesso.');
    }

    public function destroy(Project $project): RedirectResponse
    {
        $project->delete();

        return redirect()->route('admin.projects.index')->with('success', 'Projecto movido para a reciclagem.');
    }

    public function restore(int $project): RedirectResponse
    {
        Project::onlyTrashed()->findOrFail($project)->restore();

        return redirect()->route('admin.projects.index')->with('success', 'Projecto restaurado.');
    }

    private function syncRelations(Project $project, ProjectRequest $request): void
    {
        $project->categories()->sync($request->input('categories', []));
        $project->services()->sync($request->input('services', []));
        $project->syncGallery($request->input('gallery', []));
    }

    /**
     * @return array<string, mixed>
     */
    private function formData(Project $project): array
    {
        return [
            'project' => $project,
            'clients' => Client::orderBy('name')->get(['id', 'name']),
            'categories' => ProjectCategory::orderBy('sort_order')->orderBy('name')->get(),
            'services' => Service::ordered()->get(['id', 'name']),
        ];
    }
}
