<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Posts Table
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->string('featured_image')->nullable();
            $table->string('layout_template')->default('standard');
            $table->decimal('trending_score', 8, 4)->default(0.0000);
            $table->string('status', 30)->default('draft');
            $table->json('comment_rules')->nullable();
            $table->boolean('sitemap_enabled')->default(true);
            $table->string('fact_check_status', 50)->nullable();
            $table->integer('read_time_minutes')->unsigned()->default(0);
            $table->timestamp('published_at')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index(['status', 'published_at']);
            $table->index('trending_score');
            $table->index('published_at');
        });

        // Post Translations Table
        Schema::create('post_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_id')->constrained('posts')->onDelete('cascade');
            $table->string('locale', 10);
            $table->string('title', 500);
            $table->string('slug', 500);
            $table->text('excerpt')->nullable();
            $table->json('content');
            $table->string('seo_title', 255)->nullable();
            $table->string('seo_description', 500)->nullable();
            $table->json('seo_keywords')->nullable();
            $table->json('social_meta')->nullable();
            $table->json('schema_markup')->nullable();
            $table->text('editorial_notes')->nullable();
            $table->text('publisher_notes')->nullable();
            $table->timestamps();

            $table->unique(['post_id', 'locale']);
            $table->unique(['slug', 'locale']);
        });

        // Categories Table
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->nullable()->constrained('categories')->onDelete('set null');
            $table->integer('order')->unsigned()->default(0);
            $table->timestamps();
        });

        // Category Translations Table
        Schema::create('category_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained('categories')->onDelete('cascade');
            $table->string('locale', 10);
            $table->string('name', 255);
            $table->string('slug', 255);
            $table->text('description')->nullable();
            $table->timestamps();

            $table->unique(['category_id', 'locale']);
            $table->unique(['slug', 'locale']);
        });

        // Tags Table
        Schema::create('tags', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
        });

        // Tag Translations Table
        Schema::create('tag_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tag_id')->constrained('tags')->onDelete('cascade');
            $table->string('locale', 10);
            $table->string('name', 255);
            $table->string('slug', 255);
            $table->timestamps();

            $table->unique(['tag_id', 'locale']);
            $table->unique(['slug', 'locale']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tag_translations');
        Schema::dropIfExists('tags');
        Schema::dropIfExists('category_translations');
        Schema::dropIfExists('categories');
        Schema::dropIfExists('post_translations');
        Schema::dropIfExists('posts');
    }
};
