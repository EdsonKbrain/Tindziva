<?php

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

// Tasks run inside the scheduler process (closures) instead of child processes, because the
// shared hosting server disables proc_open, which Schedule::command() needs.
Schedule::call(fn () => Artisan::call('posts:publish-scheduled'))
    ->name('posts:publish-scheduled')
    ->everyMinute()
    ->withoutOverlapping();

Schedule::call(fn () => Artisan::call('model:prune', ['--model' => [ActivityLog::class]]))
    ->name('model:prune activity logs')
    ->daily();
