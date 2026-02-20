<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Recipe;
use App\Models\Tag;
use Illuminate\Support\Facades\Auth;

class RecipeController extends Controller
{
    // レシピ一覧ページ
    public function index()
    {
        // ログインしているユーザーのレシピだけを取得
        // 新しい順に並べる
        $recipes = Recipe::where('user_id', Auth::id()) //とってくるデータの条件
            ->orderBy('created_at', 'desc') //表示する条件
            ->get(); //実行命令

        // recipes/index.blade.php に $recipes を渡す
        return view('recipes.index', compact('recipes'));
    }

    //レシピ追加ページ
    public function create()
    {
        // 全てのタグを取得
        $tags = Tag::all();

        // recipes/create.blade.php に $tags を渡す
        return view('recipes.create', compact('tags'));
    }

    //レシピを保存
    public function store(Request $request)
    {
        // バリデーション（入力チェック）
        $validated = $request->validate([
            'title' => 'required|max:255',
            'memo' => 'nullable',
            'source_url' => 'nullable|url',
            'image' => 'nullable|image|max:5120', // 最大5MB
        ]);

        // レシピを作成
        $recipe = Recipe::create([
            'user_id' => Auth::id(),
            'title' => $validated['title'],
            'memo' => $validated['memo'],
            'source_url' => $validated['source_url'],
        ]);

        // 画像がアップロードされていたら保存
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('recipes', 'public');
            $recipe->image_path = $path;
            $recipe->save();
        }

        // レシピ一覧ページにリダイレクト
        return redirect()->route('recipes.index')->with('success', 'レシピを追加しました！');
    }
}
