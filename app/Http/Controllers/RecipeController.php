<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Recipe;
use Illuminate\Support\Facades\Auth;

class RecipeController extends Controller
{
    /**
     * レシピ一覧ページを表示
     */
    public function index()
    {
        // ログインしているユーザーのレシピだけを取得
        // 新しい順に並べる
        $recipes = Recipe::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        // recipes/index.blade.php に $recipes を渡す
        return view('recipes.index', compact('recipes'));
    }
}