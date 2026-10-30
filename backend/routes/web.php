<?php

use App\Http\Controllers\Admin as A;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => view('welcome'));
Route::get('/login', fn () => redirect()->route('admin.login'))->name('login');

Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('login', [A\AuthController::class, 'showLogin'])->name('login');
    Route::post('login', [A\AuthController::class, 'login'])->middleware('throttle:10,1')->name('login.post');

    Route::middleware('admin')->group(function () {
        Route::post('logout', [A\AuthController::class, 'logout'])->name('logout');
        Route::get('/', [A\DashboardController::class, 'index'])->name('dashboard');
        Route::get('search', A\SearchController::class)->name('search');

        Route::resource('projects', A\ProjectController::class)->except('show');
        Route::resource('posts', A\PostController::class)->except('show');
        Route::resource('categories', A\CategoryController::class)->except('show');
        Route::resource('skills', A\SkillController::class)->except('show');
        Route::resource('services', A\ServiceController::class)->except('show');
        Route::resource('timeline', A\TimelineController::class)->except('show');
        Route::resource('faqs', A\FaqController::class)->except('show');
        Route::resource('colors', A\ColorController::class)->except('show');

        Route::get('messages/export', [A\MessageController::class, 'export'])->name('messages.export');
        Route::post('messages/sync', [A\MessageController::class, 'sync'])->name('messages.sync');
        Route::resource('messages', A\MessageController::class)->only(['index', 'show', 'update', 'destroy']);

        Route::get('subscribers', [A\SubscriberController::class, 'index'])->name('subscribers.index');
        Route::get('subscribers/export', [A\SubscriberController::class, 'export'])->name('subscribers.export');
        Route::delete('subscribers/{subscriber}', [A\SubscriberController::class, 'destroy'])->name('subscribers.destroy');

        Route::get('media', [A\MediaController::class, 'index'])->name('media.index');
        Route::post('media', [A\MediaController::class, 'store'])->name('media.store');
        Route::get('media/list', [A\MediaController::class, 'list'])->name('media.list');
        Route::post('media/editor-upload', [A\MediaController::class, 'editorUpload'])->name('media.editor');
        Route::delete('media/{medium}', [A\MediaController::class, 'destroy'])->name('media.destroy');

        Route::get('settings', [A\SettingsController::class, 'show'])->name('settings');
        Route::post('settings', [A\SettingsController::class, 'update'])->name('settings.update');
        Route::get('profile', [A\ProfileController::class, 'show'])->name('profile');
        Route::post('profile', [A\ProfileController::class, 'update'])->name('profile.update');
    });
});
