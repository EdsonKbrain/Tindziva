<?php

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Schedule;

Schedule::command('posts:publish-scheduled')->everyMinute()->withoutOverlapping();
Schedule::command('model:prune', ['--model' => [ActivityLog::class]])->daily();
