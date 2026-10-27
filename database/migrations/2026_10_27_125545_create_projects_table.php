<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('excerpt', 500)->nullable();
            $table->longText('description')->nullable();
            $table->foreignId('cover_image_id')->nullable()->constrained('media')->nullOnDelete();
            $table->json('video_urls')->nullable();
            $table->json('credits')->nullable();
            $table->string('location')->nullable();
            $table->date('completed_at')->nullable();
            $table->boolean('show_client')->default(false);
            $table->boolean('show_credits')->default(false);
            $table->string('usage_permission', 500)->nullable();
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_published')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->string('meta_title')->nullable();
            $table->string('meta_description', 320)->nullable();
            $table->foreignId('og_image_id')->nullable()->constrained('media')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['is_published', 'published_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
