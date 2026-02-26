<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Recipe;
use App\Models\Tag;
use App\Models\Ingredient;
use App\Models\RecipeIngredient;
use App\Models\Step;
use Illuminate\Support\Facades\Auth;
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
            'source_url'         => 'nullable|url',
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
        $this->saveTags($recipe, $request);

        // 材料を保存
        $this->saveIngredients($recipe, $request);

        // 手順を保存
        $this->saveSteps($recipe, $request);

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
            'source_url'         => 'nullable|url',
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
        $this->saveTags($recipe, $request);
        $this->saveIngredients($recipe, $request);
        $this->saveSteps($recipe, $request);

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
