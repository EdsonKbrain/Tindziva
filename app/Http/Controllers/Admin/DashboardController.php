<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Permission;
use App\Enums\PostStatus;
use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\ContactMessage;
use App\Models\Post;
use App\Models\Project;
use App\Models\QuoteRequest;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(Request $request): View
    {
        $user = $request->user();
        $canSeeAllPosts = $user->hasPermission(Permission::ManageAllPosts);
        $posts = Post::query()->when(! $canSeeAllPosts, fn ($query) => $query->where('user_id', $user->id));

        $postCounts = (clone $posts)->selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status');

        return view('admin.dashboard', [
            'stats' => [
                'posts' => $postCounts->sum(),
                'published' => (int) ($postCounts[PostStatus::Published->value] ?? 0),
                'drafts' => (int) ($postCounts[PostStatus::Draft->value] ?? 0),
                'scheduled' => (int) ($postCounts[PostStatus::Scheduled->value] ?? 0),
                'projects' => Project::count(),
                'services' => Service::published()->count(),
                'quotes' => QuoteRequest::count(),
                'openQuotes' => QuoteRequest::open()->count(),
                'unreadMessages' => ContactMessage::open()->count(),
            ],
            'latestQuotes' => $user->hasPermission(Permission::ManageLeads)
                ? QuoteRequest::with('service')->latest()->limit(5)->get()
                : collect(),
            'latestMessages' => $user->hasPermission(Permission::ManageLeads)
                ? ContactMessage::latest()->limit(5)->get()
                : collect(),
            'recentPosts' => (clone $posts)->with('author')->latest('updated_at')->limit(5)->get(),
            'activity' => $user->hasPermission(Permission::ViewActivity)
                ? ActivityLog::with('user')->latest('created_at')->limit(8)->get()
                : collect(),
            'analyticsEnabled' => filled(setting('analytics_id')),
        ]);
    }
}
