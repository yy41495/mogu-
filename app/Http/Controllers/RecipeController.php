<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Recipe;
use App\Models\Tag;
use App\Models\Ingredient;
use App\Models\RecipeIngredient;
use App\Models\Step;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class RecipeController extends Controller
{
    /**
     * レシピ一覧ページを表示
     */
    public function index(Request $request)
    {
        // ログインしているユーザーのレシピを取得
        $query = Recipe::where('user_id', Auth::id())
            ->with(['tags', 'recipeIngredients.ingredient']);

        // キーワード検索
        if ($request->filled('keyword')) {
            $keyword = $request->keyword;
            $query->where(function ($q) use ($keyword) {
                // タイトル・メモで検索
                $q->where('title', 'ILIKE', "%{$keyword}%")
                    ->orWhere('memo', 'ILIKE', "%{$keyword}%")
                    // タグで検索
                    ->orWhereHas('tags', function ($tagQuery) use ($keyword) {
                        $tagQuery->where('name', 'ILIKE', "%{$keyword}%");
                    })
                    // 材料で検索
                    ->orWhereHas('recipeIngredients.ingredient', function ($ingredientQuery) use ($keyword) {
                        $ingredientQuery->where('name', 'ILIKE', "%{$keyword}%");
                    });
            });
        }

        // タグでフィルター
        if ($request->filled('tags')) {
            $tagIds = $request->tags;
            $query->whereHas('tags', function ($tagQuery) use ($tagIds) {
                $tagQuery->whereIn('tags.id', $tagIds);
            });
        }

        // 新しい順に並べる
        $recipes = $query->orderBy('created_at', 'desc')->get();

        // 全てのタグを取得（フィルター用）
        $allTags = Tag::all();

        return view('recipes.index', compact('recipes', 'allTags'));
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
            'title'              => 'required|max:255',
            'memo'               => 'nullable',
            'source_url'         => 'nullable|string|max:255',
            'image'              => 'nullable|image|max:5120',
            'ogp_image_url'      => 'nullable|url',
            'tags'               => 'nullable|array',
            'new_tags'           => 'nullable|array',
            'new_tags.*'         => 'nullable|string|max:50',
            'new_tag_colors'     => 'nullable|array',
            'new_tag_colors.*'   => 'nullable|string|max:7', // #ffffff 形式
            'ingredients'        => 'nullable|array',
            'steps'              => 'nullable|array',
        ]);

        //レシピ保存後に材料保存中でエラーが起きると、レシピだけが存在して材料がない中途半端なデータが残ってしまう。
        //それを防ぐためにtransactionを使用して、レシピ保存、材料保存、手順保存を一度に行う。
        $recipe = DB::transaction(function () use ($request, $validated) {

            // レシピを作成
            $recipe = Recipe::create([
                'user_id' => Auth::id(),
                'title' => $validated['title'],
                'memo' => $validated['memo'] ?? null,
                'source_url' => $validated['source_url'] ?? null,
            ]);

            // 画像がアップロードされていたら Cloudinary に保存
            if ($request->hasFile('image')) {
                $uploadedFile = $request->file('image');
                $uploadResult = Cloudinary::upload($uploadedFile->getRealPath(), [
                    'folder' => 'mogu-plus/recipes',
                    'transformation' => [
                        'width' => 1200,
                        'height' => 1200,
                        'crop' => 'limit',
                        'quality' => 'auto',
                        'fetch_format' => 'auto'
                    ]
                ]);

                $recipe->image_path = $uploadResult->getSecurePath();
                $recipe->save();
            }

            // OGP画像URLが送られてきたら保存（画像アップロードがない場合のみ）
            if (!$request->hasFile('image') && $request->filled('ogp_image_url')) {
                $recipe->image_path = $request->input('ogp_image_url');
                $recipe->save();
            }

            // タグを保存
            $this->saveTags($recipe, $validated);

            // 材料を保存
            $this->saveIngredients($recipe, $validated);

            // 手順を保存
            $this->saveSteps($recipe, $validated);

            return $recipe; // ← transactionの外に$recipeを渡すためにreturnが必要
            //現状必要ないけど将来必要な時のために一応記述しておく
        });

        // レシピ一覧ページにリダイレクト
        return redirect()->route('recipes.index')->with('success', 'レシピを追加しました！');
    }

    /**
     * レシピ詳細ページを表示
     */
    public function show($id)
    {
        // レシピを取得（ログインユーザーのものだけ）
        $recipe = Recipe::where('id', $id)
            ->where('user_id', Auth::id())
            ->with(['tags', 'recipeIngredients.ingredient', 'steps'])
            ->firstOrFail();

        return view('recipes.show', compact('recipe'));
    }

    /**
     * レシピ編集ページを表示
     */
    public function edit($id)
    {
        // レシピを取得（ログインユーザーのものだけ）
        $recipe = Recipe::where('id', $id)
            ->where('user_id', Auth::id())
            ->with(['tags', 'recipeIngredients.ingredient', 'steps'])
            ->firstOrFail();

        // 全てのタグを取得
        $tags = Tag::all();

        return view('recipes.edit', compact('recipe', 'tags'));
    }

    /**
     * レシピを更新
     */
    public function update(Request $request, $id)
    {
        // レシピを取得（ログインユーザーのものだけ）
        $recipe = Recipe::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        // バリデーション
        $validated = $request->validate([
            'title'              => 'required|max:255',
            'memo'               => 'nullable',
            'source_url'         => 'nullable|string|max:255',
            'image'              => 'nullable|image|max:5120',
            'ogp_image_url'      => 'nullable|url',
            'tags'               => 'nullable|array',
            'new_tags'           => 'nullable|array',
            'new_tags.*'         => 'nullable|string|max:50',
            'new_tag_colors'     => 'nullable|array',
            'new_tag_colors.*'   => 'nullable|string|max:7',
            'ingredients'        => 'nullable|array',
            'steps'              => 'nullable|array',
        ]);


        // ↓ こっちもDB::transaction() で囲む
        DB::transaction(function () use ($request, $validated, $recipe) {
            // レシピを更新
            $recipe->update([
                'title' => $validated['title'],
                'memo' => $validated['memo'] ?? null,
                'source_url' => $validated['source_url'] ?? null,
            ]);

            // 画像がアップロードされていたら Cloudinary に保存
            if ($request->hasFile('image')) {
                // 古い画像を Cloudinary から削除
                if ($recipe->image_path && str_contains($recipe->image_path, 'cloudinary.com')) {
                    $publicId = $this->getPublicIdFromUrl($recipe->image_path);
                    if ($publicId) {
                        Cloudinary::destroy($publicId);
                    }
                }

                // 新しい画像をアップロード
                $uploadedFile = $request->file('image');
                $uploadResult = Cloudinary::upload($uploadedFile->getRealPath(), [
                    'folder' => 'mogu-plus/recipes',
                    'transformation' => [
                        'width' => 1200,
                        'height' => 1200,
                        'crop' => 'limit',
                        'quality' => 'auto',
                        'fetch_format' => 'auto'
                    ]
                ]);

                $recipe->image_path = $uploadResult->getSecurePath();
                $recipe->save();
            }

            // OGP画像URLが送られてきたら保存（画像アップロードがない場合のみ）
            if (!$request->hasFile('image') && $request->filled('ogp_image_url')) {
                $recipe->image_path = $request->input('ogp_image_url');
                $recipe->save();
            }

            // 既存のタグ・材料・手順を削除
            $recipe->tags()->detach();
            $recipe->recipeIngredients()->delete();
            $recipe->steps()->delete();

            // 新しいデータを保存
            $this->saveTags($recipe, $validated);
            $this->saveIngredients($recipe, $validated);
            $this->saveSteps($recipe, $validated);
        });
        // ↑ DB::transaction() で囲む（ここまで）

        // レシピ詳細ページにリダイレクト
        return redirect()->route('recipes.show', $recipe->id)->with('success', 'レシピを更新しました！');
    }

    /**
     * レシピを削除
     */
    public function destroy($id)
    {
        // レシピを取得（ログインユーザーのものだけ）
        $recipe = Recipe::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        // Cloudinary から画像を削除
        if ($recipe->image_path && str_contains($recipe->image_path, 'cloudinary.com')) {
            $publicId = $this->getPublicIdFromUrl($recipe->image_path);
            if ($publicId) {
                Cloudinary::destroy($publicId);
            }
        }

        // レシピを削除（ソフトデリート）
        $recipe->delete();

        // レシピ一覧ページにリダイレクト
        return redirect()->route('recipes.index')->with('success', 'レシピを削除しました！');
    }

    /**
     * タグを保存
     */
    private function saveTags($recipe, $validated)
    {
        $tagIds = [];

        // 既存タグ
        if (!empty($validated['tags'])) {
            $tagIds = array_merge($tagIds, $validated['tags']);
        }

        // 新規タグ
        if (!empty($validated['new_tags'])) {
            foreach ($validated['new_tags'] as $index => $tagName) {
                // 色情報も取得（JavaScriptから送信される）
                $color = $validated['new_tag_colors'][$index] ?? '#e0e0e0';

                // 同じ名前のタグがあるか確認
                $tag = Tag::firstOrCreate(
                    ['name' => $tagName],
                    ['color' => $color]
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
    private function saveIngredients($recipe, $validated)
    {
        if (empty($validated['ingredients'])) {
            return;
        }

        foreach ($validated['ingredients'] as $ingredientData) {
            // 空の材料はスキップ
            if (empty($ingredientData['name'])) {
                continue;
            }

            // 材料マスターに登録（すでにあれば取得）
            $ingredient = Ingredient::firstOrCreate([
                'name' => $ingredientData['name']
            ]);

            // レシピと材料を紐付け
            RecipeIngredient::updateOrCreate(
                // 「このrecipe_idとingredient_idの組み合わせ」を検索
                [
                    'recipe_id'     => $recipe->id,
                    'ingredient_id' => $ingredient->id,
                ],
                // あれば量を更新、なければ新規作成
                [
                    'quantity' => $ingredientData['quantity'] ?? null,
                ]
            );
        }
    }

    /**
     * 手順を保存
     */
    private function saveSteps($recipe, $validated)
    {
        if (empty($validated['steps'])) {
            return;
        }

        $stepNumber = 1;
        foreach ($validated['steps'] as $stepData) {
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

    /**
     * Cloudinary の URL から public_id を取得
     */
    private function getPublicIdFromUrl($url)
    {
        // https://res.cloudinary.com/xxx/image/upload/v123456/mogu-plus/recipes/abc123.jpg
        // → 例：mogu-plus/recipes/abc123

        $pattern = '/\/upload\/(?:v\d+\/)?(.+)\.\w+$/';
        if (preg_match($pattern, $url, $matches)) {
            return $matches[1];
        }
        return null;
    }
}
