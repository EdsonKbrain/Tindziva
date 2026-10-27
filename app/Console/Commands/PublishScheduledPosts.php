<?php

namespace App\Console\Commands;

use App\Enums\PostStatus;
use App\Models\Post;
use App\Services\ActivityLogger;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('posts:publish-scheduled')]
#[Description('Publish scheduled blog posts whose publication date has arrived')]
class PublishScheduledPosts extends Command
{
    public function handle(ActivityLogger $logger): int
    {
        $published = 0;

        Post::dueForPublication()->each(function (Post $post) use ($logger, &$published): void {
            $post->update(['status' => PostStatus::Published]);
            $logger->log('published', "Publicou automaticamente o artigo «{$post->title}»", $post);
            $published++;
        });

        $this->components->info("{$published} artigo(s) publicado(s).");

        return self::SUCCESS;
    }
}
