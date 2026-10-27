<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ActivityLogController extends Controller
{
    public function __invoke(Request $request): View
    {
        $logs = ActivityLog::query()
            ->with('user')
            ->when($request->filled('user'), fn ($query) => $query->where('user_id', $request->integer('user')))
            ->when($request->filled('action'), fn ($query) => $query->where('action', $request->input('action')))
            ->when($request->filled('q'), fn ($query) => $query->where('description', 'like', '%'.$request->string('q').'%'))
            ->latest('created_at')
            ->latest('id')
            ->paginate(40)
            ->withQueryString();

        return view('admin.activity.index', [
            'logs' => $logs,
            'users' => User::orderBy('name')->get(['id', 'name']),
            'actions' => ActivityLog::query()->distinct()->orderBy('action')->pluck('action'),
        ]);
    }
}
