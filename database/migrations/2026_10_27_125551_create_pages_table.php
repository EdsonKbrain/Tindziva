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
        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('template', 32)->default('default');
            $table->string('subtitle', 500)->nullable();
            $table->longText('content')->nullable();
            $table->json('sections')->nullable();
            $table->foreignId('hero_image_id')->nullable()->constrained('media')->nullOnDelete();
            $table->boolean('is_published')->default(true);
            $table->boolean('is_system')->default(false);
            $table->boolean('show_in_menu')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->string('meta_title')->nullable();
            $table->string('meta_description', 320)->nullable();
            $table->foreignId('og_image_id')->nullable()->constrained('media')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pages');
    }
};
