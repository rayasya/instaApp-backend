<?php

use App\Http\Controllers\Api\ActivityPageController;
use App\Http\Controllers\Api\AddPostController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ExplorePageController;
use App\Http\Controllers\Api\HomePageController;
use App\Http\Controllers\Api\ProfilePageController;
use Illuminate\Support\Facades\Route;

Route::post('login', [AuthController::class, 'login'])->name('login');
Route::post('register', [AuthController::class, 'register'])->name('register');

Route::middleware(['auth:sanctum'])->group(function () {
    // Home Page API
    Route::get('posts', [HomePageController::class, 'posts'])->name('posts');
    Route::post('like/{postId}', [HomePageController::class, 'like'])->name('like');
    Route::get('comment/{postId}', [HomePageController::class, 'getComments'])->name('getComments');
    Route::post('comment/{postId}', [HomePageController::class, 'comment'])->name('comment');
    Route::post('save/{postId}', [HomePageController::class, 'save'])->name('save');

    // Explore Page API
    Route::get('explore', [ExplorePageController::class, 'explore'])->name('explore');
    Route::get('post/{postId}', [ExplorePageController::class, 'postDetail'])->name('postDetail');

    // Add Post API
    Route::post('addPost', [AddPostController::class, 'addPost'])->name('addPost');

    // Activity Page API
    Route::get('activity', [ActivityPageController::class, 'activity'])->name('activity');

    // Profile Page API
    Route::get('profile/{username}', [ProfilePageController::class, 'profile'])->name('profile');
    Route::post('editProfile', [ProfilePageController::class, 'editProfile'])->name('editProfile');

    Route::post('logout', [AuthController::class, 'logout'])->name('logout');
});
