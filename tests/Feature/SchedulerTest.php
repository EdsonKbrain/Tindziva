<?php

namespace Tests\Feature;

use App\Enums\PostStatus;
use App\Models\Post;
use Illuminate\Console\Scheduling\CallbackEvent;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SchedulerTest extends TestCase
{
    use RefreshDatabase;

    public function test_scheduled_tasks_run_in_process_without_proc_open(): void
    {
        $events = app(Schedule::class)->events();

        $this->assertNotEmpty($events);
        foreach ($events as $event) {
            $this->assertInstanceOf(CallbackEvent::class, $event, "A tarefa «{$event->description}» precisaria de proc_open.");
        }
    }

    public function test_schedule_run_publishes_due_posts(): void
    {
        $post = Post::factory()->scheduled(now()->subMinute())->create();

        $this->artisan('schedule:run')->assertSuccessful();

        $this->assertSame(PostStatus::Published, $post->fresh()->status);
    }
}
