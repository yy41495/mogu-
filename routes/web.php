<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\RecipeController;

// 既存のルート
Route::get('/', function () {
    return view('welcome');
});

// Googleログイン
Route::get('/auth/google', [GoogleController::class, 'redirectToGoogle']);
Route::get('/auth/google/callback', [GoogleController::class, 'handleGoogleCallback']);

// ログインページ
Route::get('/login', function () {
    return view('login');
});

// ↓↓↓ ログインしているユーザーだけがアクセスできるページ ↓↓↓
Route::middleware(['auth'])->group(function () {
    // レシピ一覧ページ
    Route::get('/recipes', [RecipeController::class, 'index'])->name('recipes.index');

    // ↓ これから作るページは全部ここに追加していく！
    // 例: レシピ追加ページ
    // Route::get('/recipes/create', [RecipeController::class, 'create'])->name('recipes.create');

    // 例: レシピ詳細ページ
    // Route::get('/recipes/{id}', [RecipeController::class, 'show'])->name('recipes.show');
});