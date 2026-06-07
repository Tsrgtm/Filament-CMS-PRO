<?php

use Illuminate\Support\Facades\Route;
use Nepal360\FilamentCmsPro\Models\Post;
use Nepal360\FilamentCmsPro\Support\CmsFacade;

Route::get('/posts/{slug}', function (string $slug) {
    $locale = app()->getLocale();
    
    // Find the post matching translation slug
    $post = Post::whereHas('translations', function ($query) use ($slug, $locale) {
        $query->where('slug', $slug)->where('locale', $locale);
    })->where('status', 'published')->firstOrFail();

    return CmsFacade::renderPost($post, $locale);
})->middleware('web');
