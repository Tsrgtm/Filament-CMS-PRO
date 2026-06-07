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
        Schema::table('posts', function (Blueprint $table) {
            $table->json('custom_fields')->nullable();
        });

        Schema::table('post_translations', function (Blueprint $table) {
            $table->json('custom_fields')->nullable();
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->json('custom_fields')->nullable();
        });

        Schema::table('category_translations', function (Blueprint $table) {
            $table->json('custom_fields')->nullable();
        });

        Schema::table('tags', function (Blueprint $table) {
            $table->json('custom_fields')->nullable();
        });

        Schema::table('tag_translations', function (Blueprint $table) {
            $table->json('custom_fields')->nullable();
        });

        Schema::table('comments', function (Blueprint $table) {
            $table->json('custom_fields')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn('custom_fields');
        });

        Schema::table('post_translations', function (Blueprint $table) {
            $table->dropColumn('custom_fields');
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn('custom_fields');
        });

        Schema::table('category_translations', function (Blueprint $table) {
            $table->dropColumn('custom_fields');
        });

        Schema::table('tags', function (Blueprint $table) {
            $table->dropColumn('custom_fields');
        });

        Schema::table('tag_translations', function (Blueprint $table) {
            $table->dropColumn('custom_fields');
        });

        Schema::table('comments', function (Blueprint $table) {
            $table->dropColumn('custom_fields');
        });
    }
};
