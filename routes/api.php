<?php

use Illuminate\Support\Facades\Route;
use Nepal360\FilamentCmsPro\API\Controllers\PostApiController;
use Nepal360\FilamentCmsPro\API\Controllers\CommentApiController;

Route::get('/posts', [PostApiController::class, 'index']);
Route::get('/posts/{slug}', [PostApiController::class, 'show']);
Route::post('/posts/{post}/transition', [PostApiController::class, 'transition']);

Route::get('/categories', [PostApiController::class, 'categories']);
Route::get('/tags', [PostApiController::class, 'tags']);

Route::get('/posts/{post}/comments', [CommentApiController::class, 'index']);
Route::post('/posts/{post}/comments', [CommentApiController::class, 'store']);
