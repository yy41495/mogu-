<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\GoogleController;

// 既存のルート...
Route::get('/', function () {
    return view('welcome');
});

// ↓ ここから追加！

// Googleログイン
Route::get('/auth/google', [GoogleController::class, 'redirectToGoogle']);
Route::get('/auth/google/callback', [GoogleController::class, 'handleGoogleCallback']);

// ログインページ（仮）
Route::get('/login', function () {
    return view('login');
});

// レシピ一覧ページ（仮）
Route::get('/recipes', function () {
    return 'ログイン成功！レシピ一覧ページ（まだ作ってない）';
})->middleware('auth');