<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Revisions Table
        Schema::create('revisions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_id')->constrained('posts')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('locale', 10);
            $table->string('title', 500);
            $table->text('excerpt')->nullable();
            $table->json('content');
            $table->timestamp('created_at')->useCurrent();

            $table->index(['post_id', 'locale']);
        });

        // Comments Table (Nested Support)
        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_id')->constrained('posts')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('parent_id')->nullable()->constrained('comments')->onDelete('cascade');
            $table->string('author_name', 255)->nullable();
            $table->string('author_email', 255)->nullable();
            $table->text('content');
            $table->string('status', 30)->default('pending');
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent', 500)->nullable();
            $table->timestamps();

            $table->index(['post_id', 'status']);
            $table->index('parent_id');
        });

        // Content Locks Table
        Schema::create('content_locks', function (Blueprint $table) {
            $table->foreignId('post_id')->primary()->constrained('posts')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->timestamp('locked_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('content_locks');
        Schema::dropIfExists('comments');
        Schema::dropIfExists('revisions');
    }
};
