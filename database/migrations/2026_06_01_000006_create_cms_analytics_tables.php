<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Analytics Page Views
        Schema::create('analytics_page_views', function (Blueprint $table) {
            $table->id();
            $table->foreignId('post_id')->nullable()->constrained('posts')->onDelete('set null');
            $table->string('path', 1000);
            $table->string('referrer_host', 255)->nullable();
            $table->char('visitor_hash', 64);
            $table->boolean('is_robot')->default(false);
            $table->string('utm_source', 100)->nullable();
            $table->string('utm_medium', 100)->nullable();
            $table->string('utm_campaign', 100)->nullable();
            $table->timestamp('viewed_at')->useCurrent();

            $table->index(['post_id', 'viewed_at']);
            $table->index(['is_robot', 'viewed_at']);
            $table->index(['visitor_hash', 'viewed_at']);
        });

        // Analytics Engagement
        Schema::create('analytics_engagement', function (Blueprint $table) {
            $table->id();
            $table->foreignId('page_view_id')->unique()->constrained('analytics_page_views')->onDelete('cascade');
            $table->integer('scroll_depth_percentage')->unsigned()->default(0);
            $table->integer('time_on_page_seconds')->unsigned()->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('analytics_engagement');
        Schema::dropIfExists('analytics_page_views');
    }
};
