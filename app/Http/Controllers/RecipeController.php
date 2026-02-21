<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Recipe;
use App\Models\Tag;
use App\Models\Ingredient;           // ← 追加
use App\Models\RecipeIngredient;     // ← 追加
use App\Models\Step;                 // ← 追加
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

    /**
     * レシピを保存
     */
    public function store(Request $request)
    {
        // バリデーション（入力チェック）
        $validated = $request->validate([
            'title' => 'required|max:255',
            'memo' => 'nullable',
            'source_url' => 'nullable|url',
            'image' => 'nullable|image|max:5120', // 最大5MB
            'tags' => 'nullable|array',
            'new_tags' => 'nullable|array',
            'ingredients' => 'nullable|array',
            'steps' => 'nullable|array',
        ]);

        // レシピを作成
        $recipe = Recipe::create([
            'user_id' => Auth::id(),
            'title' => $validated['title'],
            'memo' => $validated['memo'] ?? null,
            'source_url' => $validated['source_url'] ?? null,
        ]);

        // 画像がアップロードされていたら保存
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('recipes', 'public');
            $recipe->image_path = $path;
            $recipe->save();
        }

        // タグを保存
        $this->saveTags($recipe, $request);

        // 材料を保存
        $this->saveIngredients($recipe, $request);

        // 手順を保存
        $this->saveSteps($recipe, $request);

        // レシピ一覧ページにリダイレクト
        return redirect()->route('recipes.index')->with('success', 'レシピを追加しました！');
    }

    /**
     * タグを保存
     */
    private function saveTags($recipe, $request)
    {
        $tagIds = [];

        // 既存タグ
        if ($request->has('tags')) {
            $tagIds = array_merge($tagIds, $request->tags);
        }

        // 新規タグ
        if ($request->has('new_tags')) {
            foreach ($request->new_tags as $index => $tagName) {
                // 色情報も取得（JavaScriptから送信される）
                $color = $request->input('new_tag_colors.' . $index, '#e0e0e0');

                // 同じ名前のタグがあるか確認
                $tag = Tag::firstOrCreate(
                    ['name' => $tagName],
                    ['color' => $color]  // 新規作成時に色も保存
                );
                $tagIds[] = $tag->id;
            }
        }

        // レシピとタグを紐付け
        if (!empty($tagIds)) {
            $recipe->tags()->attach($tagIds);
        }
    }

    /**
     * 材料を保存
     */
    private function saveIngredients($recipe, $request)
    {
        if (!$request->has('ingredients')) {
            return;
        }

        foreach ($request->ingredients as $ingredientData) {
            // 空の材料はスキップ
            if (empty($ingredientData['name'])) {
                continue;
            }

            // 材料マスターに登録（すでにあれば取得）
            $ingredient = Ingredient::firstOrCreate([
                'name' => $ingredientData['name']
            ]);

            // レシピと材料を紐付け
            RecipeIngredient::create([
                'recipe_id' => $recipe->id,
                'ingredient_id' => $ingredient->id,
                'quantity' => $ingredientData['quantity'] ?? null,
            ]);
        }
    }

    /**
     * 手順を保存
     */
    private function saveSteps($recipe, $request)
    {
        if (!$request->has('steps')) {
            return;
        }

        $stepNumber = 1;
        foreach ($request->steps as $stepData) {
            // 空の手順はスキップ
            if (empty($stepData['description'])) {
                continue;
            }

            Step::create([
                'recipe_id' => $recipe->id,
                'step_number' => $stepNumber,
                'description' => $stepData['description'],
            ]);

            $stepNumber++;
        }
    }
}
