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

    // レシピ追加ページ（入力フォーム表示）
    Route::get('/recipes/create', [RecipeController::class, 'create'])->name('recipes.create');

    // レシピ保存処理（フォーム送信時）
    Route::post('/recipes', [RecipeController::class, 'store'])->name('recipes.store');
});