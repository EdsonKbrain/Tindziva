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
        Schema::create('quote_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->nullable()->constrained()->nullOnDelete();
            $table->string('service_name')->nullable();
            $table->string('name');
            $table->string('company')->nullable();
            $table->string('email');
            $table->string('phone', 40)->nullable();
            $table->string('budget', 64)->nullable();
            $table->date('deadline')->nullable();
            $table->text('message');
            $table->string('status', 24)->default('new')->index();
            $table->text('internal_notes')->nullable();
            $table->string('source_url', 500)->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent', 500)->nullable();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quote_requests');
    }
};
