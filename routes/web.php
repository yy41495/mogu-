<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\GoogleController;
use App\Http\Controllers\RecipeController;
use App\Http\Controllers\OgpController;

// 既存のルート
Route::get('/', function () {
    return view('welcome');
});

// Googleログイン
Route::get('/auth/google', [GoogleController::class, 'redirectToGoogle']);
Route::get('/auth/google/callback', [GoogleController::class, 'handleGoogleCallback']);

// ログアウト
Route::post('/logout', [GoogleController::class, 'logout'])->name('logout');

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

    // レシピ詳細ページ
    Route::get('/recipes/{id}', [RecipeController::class, 'show'])->name('recipes.show');

    // レシピ編集ページ
    Route::get('/recipes/{id}/edit', [RecipeController::class, 'edit'])->name('recipes.edit');

    // レシピ更新処理
    Route::put('/recipes/{id}', [RecipeController::class, 'update'])->name('recipes.update');

    // レシピ削除処理
    Route::delete('/recipes/{id}', [RecipeController::class, 'destroy'])->name('recipes.destroy');

    // OGP取得API
    Route::post('/ogp/fetch', [OgpController::class, 'fetch'])->name('ogp.fetch');
});
