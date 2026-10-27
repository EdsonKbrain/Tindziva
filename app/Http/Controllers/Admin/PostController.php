<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Permission;
use App\Enums\PostStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\PostRequest;
use App\Models\Post;
use App\Models\PostCategory;
use App\Models\Tag;
use App\Models\User;
use App\Support\SeoData;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PostController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('viewAny', Post::class);

        $user = $request->user();

        $posts = Post::query()
            ->with(['author', 'category', 'coverImage'])
            ->when(! $user->hasPermission(Permission::ManageAllPosts), fn ($query) => $query->where('user_id', $user->id))
            ->when($request->boolean('trashed'), fn ($query) => $query->onlyTrashed())
            ->when($request->filled('q'), fn ($query) => $query->where('title', 'like', '%'.$request->string('q').'%'))
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->input('status')))
            ->when($request->filled('category'), fn ($query) => $query->where('post_category_id', $request->integer('category')))
            ->latest('updated_at')
            ->paginate(20)
            ->withQueryString();

        return view('admin.posts.index', [
            'posts' => $posts,
            'categories' => PostCategory::orderBy('name')->get(),
            'statuses' => PostStatus::cases(),
        ]);
    }

    public function create(Request $request): View
    {
        Gate::authorize('create', Post::class);

        return view('admin.posts.form', $this->formData(new Post([
            'status' => PostStatus::Draft,
            'user_id' => $request->user()->id,
        ])));
    }

    public function store(PostRequest $request): RedirectResponse
    {
        $post = DB::transaction(function () use ($request): Post {
            $post = new Post($request->payload());
            $post->user_id ??= $request->user()->id;
            $post->save();
            $this->syncTags($post, $request->tagNames());

            return $post;
        });

        return redirect()->route('admin.posts.edit', $post)->with('success', $this->statusMessage($post));
    }

    public function edit(Post $post): View
    {
        Gate::authorize('update', $post);

        $post->load(['tags', 'coverImage', 'ogImage']);

        return view('admin.posts.form', $this->formData($post));
    }

    public function update(PostRequest $request, Post $post): RedirectResponse
    {
        DB::transaction(function () use ($request, $post): void {
            $post->update($request->payload());
            $this->syncTags($post, $request->tagNames());
        });

        return back()->with('success', $this->statusMessage($post));
    }

    public function destroy(Post $post): RedirectResponse
    {
        Gate::authorize('delete', $post);

        $post->delete();

        return redirect()->route('admin.posts.index')->with('success', 'Artigo movido para a reciclagem.');
    }

    public function restore(int $post): RedirectResponse
    {
        $post = Post::onlyTrashed()->findOrFail($post);
        Gate::authorize('restore', $post);

        $post->restore();

        return redirect()->route('admin.posts.index')->with('success', 'Artigo restaurado.');
    }

    /**
     * Render the public article template for any status so editors can review it.
     */
    public function preview(Post $post): View
    {
        Gate::authorize('view', $post);

        $post->load(['author', 'category', 'tags', 'coverImage']);

        return view('site.blog.show', [
            'post' => $post,
            'related' => collect(),
            'isPreview' => true,
            'seo' => new SeoData(title: $post->title, noindex: true),
        ]);
    }

    /**
     * @param  list<string>  $names
     */
    private function syncTags(Post $post, array $names): void
    {
        $ids = collect($names)->map(function (string $name): int {
            $slug = Str::slug($name);

            return Tag::firstOrCreate(['slug' => $slug], ['name' => Str::limit($name, 60, '')])->id;
        });

        $post->tags()->sync($ids->all());
    }

    private function statusMessage(Post $post): string
    {
        return match ($post->status) {
            PostStatus::Published => 'Artigo publicado com sucesso.',
            PostStatus::Scheduled => 'Artigo agendado para '.$post->published_at?->format('d/m/Y H:i').'.',
            PostStatus::Draft => 'Rascunho guardado.',
        };
    }

    /**
     * @return array<string, mixed>
     */
    private function formData(Post $post): array
    {
        return [
            'post' => $post,
            'categories' => PostCategory::orderBy('sort_order')->orderBy('name')->get(),
            'authors' => User::query()
                ->where(fn ($query) => $query->where('is_active', true)->when($post->user_id, fn ($query) => $query->orWhereKey($post->user_id)))
                ->orderBy('name')
                ->get(['id', 'name']),
            'statuses' => PostStatus::cases(),
        ];
    }
}
