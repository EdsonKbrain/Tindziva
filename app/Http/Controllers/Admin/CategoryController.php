<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\CategoryRequest;
use App\Models\PostCategory;
use App\Models\ProjectCategory;
use App\Models\ServiceCategory;
use App\Models\Tag;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Manages every taxonomy (service, project and post categories plus tags) from one screen.
 */
class CategoryController extends Controller
{
    /**
     * @var array<string, array{model: class-string<Model>, label: string, singular: string, count: string}>
     */
    public const TYPES = [
        'posts' => ['model' => PostCategory::class, 'label' => 'Categorias do blog', 'singular' => 'categoria', 'count' => 'posts'],
        'projects' => ['model' => ProjectCategory::class, 'label' => 'Categorias do portfólio', 'singular' => 'categoria', 'count' => 'projects'],
        'services' => ['model' => ServiceCategory::class, 'label' => 'Categorias de serviços', 'singular' => 'categoria', 'count' => 'services'],
        'tags' => ['model' => Tag::class, 'label' => 'Etiquetas do blog', 'singular' => 'etiqueta', 'count' => 'posts'],
    ];

    public function index(Request $request, string $type = 'posts'): View
    {
        $config = self::config($type);
        $model = $config['model'];

        $items = $model::query()
            ->withCount($config['count'])
            ->when($request->filled('q'), fn ($query) => $query->where('name', 'like', '%'.$request->string('q').'%'))
            ->when($type !== 'tags', fn ($query) => $query->orderBy('sort_order'))
            ->orderBy('name')
            ->paginate(40)
            ->withQueryString();

        $editing = $request->filled('edit') ? $model::find($request->integer('edit')) : null;

        return view('admin.categories.index', [
            'type' => $type,
            'config' => $config,
            'types' => self::TYPES,
            'items' => $items,
            'editing' => $editing,
        ]);
    }

    public function store(CategoryRequest $request, string $type): RedirectResponse
    {
        self::config($type)['model']::create($request->payload());

        return redirect()->route('admin.categories.index', $type)->with('success', 'Registo criado com sucesso.');
    }

    public function update(CategoryRequest $request, string $type, int $id): RedirectResponse
    {
        self::config($type)['model']::findOrFail($id)->update($request->payload());

        return redirect()->route('admin.categories.index', $type)->with('success', 'Registo actualizado com sucesso.');
    }

    public function destroy(string $type, int $id): RedirectResponse
    {
        self::config($type)['model']::findOrFail($id)->delete();

        return redirect()->route('admin.categories.index', $type)->with('success', 'Registo eliminado.');
    }

    /**
     * @return array{model: class-string<Model>, label: string, singular: string, count: string}
     */
    public static function config(string $type): array
    {
        return self::TYPES[$type] ?? throw new NotFoundHttpException;
    }
}
