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
        Schema::create('post_tag', function (Blueprint $table) {
            $table->foreignId('post_id')->constrained()->cascadeOnDelete();
            $table->foreignId('tag_id')->constrained()->cascadeOnDelete();
            $table->primary(['post_id', 'tag_id']);
        });

        Schema::create('project_project_category', function (Blueprint $table) {
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('project_category_id')->constrained()->cascadeOnDelete();
            $table->primary(['project_id', 'project_category_id']);
        });

        Schema::create('project_service', function (Blueprint $table) {
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('service_id')->constrained()->cascadeOnDelete();
            $table->primary(['project_id', 'service_id']);
        });

        Schema::create('mediables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('media_id')->constrained()->cascadeOnDelete();
            $table->morphs('mediable');
            $table->string('collection', 32)->default('gallery');
            $table->unsignedInteger('sort_order')->default(0);

            $table->unique(['media_id', 'mediable_type', 'mediable_id', 'collection'], 'mediables_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mediables');
        Schema::dropIfExists('project_service');
        Schema::dropIfExists('project_project_category');
        Schema::dropIfExists('post_tag');
    }
};
