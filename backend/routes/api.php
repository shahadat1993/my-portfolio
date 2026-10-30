<?php

use App\Http\Controllers\Api\PublicController;
use Illuminate\Support\Facades\Route;

Route::get('bootstrap', [PublicController::class, 'bootstrap']);
Route::get('image/{path}', [PublicController::class, 'image'])->where('path', '.+');
Route::get('projects', [PublicController::class, 'projects']);
Route::get('projects/{slug}', [PublicController::class, 'project']);
Route::get('posts', [PublicController::class, 'posts']);
Route::get('posts/{slug}', [PublicController::class, 'post']);
Route::get('search', [PublicController::class, 'search']);
Route::post('contact', [PublicController::class, 'contact'])->middleware('throttle:contact');
Route::post('subscribe', [PublicController::class, 'subscribe'])->middleware('throttle:contact');
