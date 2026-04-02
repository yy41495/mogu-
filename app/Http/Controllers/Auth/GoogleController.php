<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Http\Request;

class GoogleController extends Controller
{
    /**
     * Googleログインページへリダイレクト
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Googleからのコールバック処理
     */
    public function handleGoogleCallback()
    {
        try {
            // Googleからユーザー情報を取得
            $googleUser = Socialite::driver('google')->user();

            // データベースでユーザーを探す or 新規作成
            $isNewUser = !User::where('google_id', $googleUser->getId())->exists();

            $user = User::updateOrCreate(
                ['google_id' => $googleUser->getId()],
                [
                    'name' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                ]
            );

            // 新規ユーザーならサンプルレシピを作成
            if ($isNewUser) {
                $this->createSampleRecipe($user);
            }

            // ログイン
            Auth::login($user);

            // ログイン後のページへリダイレクト
            return redirect('/recipes');
        } catch (\Exception $e) {
            // eはエラー情報、将来的に必要な場合に備えて一応入れてる
            // エラーが起きたらログイン画面に戻る
            // return redirect('/login')->with('error', 'ログインに失敗しました');
            return redirect('/login')->with('error', 'ログインに失敗しました：' . $e->getMessage());
        }
    }

    /**
     * ログアウト
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login')->with('success', 'ログアウトしました');
    }

    /**
     * 新規ユーザー用サンプルレシピを作成
     */
    private function createSampleRecipe(User $user)
    {
        $tag = \App\Models\Tag::firstOrCreate(['name' => 'サンプル']);

        $recipe = \App\Models\Recipe::create([
            'user_id'    => $user->id,
            'title'      => 'README（サンプルレシピ）',
            'memo'       => "これはサンプルデータです。自由に削除してOKです！\n\nこのアプリでは、タイトル・材料・タグでレシピを検索できます。\nサムネイル一覧ページの＋ボタンから新しいレシピを追加してみてください。\n材料名に同じものは入れられないので、もし2回書く必要がある場合は表記を変えてください。（例：「醤油（下味用）、醤油（味付け）など）",
        ]);

        $recipe->tags()->attach($tag->id);

        $ingredients = [
            ['name' => '食材', 'quantity' => '適量'],
            ['name' => '調味料', 'quantity' => '少々'],
        ];

        foreach ($ingredients as $item) {
            $ingredient = \App\Models\Ingredient::firstOrCreate(['name' => $item['name']]);
            \App\Models\RecipeIngredient::create([
                'recipe_id'   => $recipe->id,
                'ingredient_id' => $ingredient->id,
                'quantity'    => $item['quantity'],
            ]);
        }

        $steps = [
            '右上の太陽アイコンのボタンはスリープ防止ボタンです',
            'サムネイル一覧ページの＋ボタンからレシピを追加できます',
            '検索バーでタイトル・材料・タグを検索できます',
            'このサンプルレシピは削除してOKです！',
        ];

        foreach ($steps as $index => $description) {
            \App\Models\Step::create([
                'recipe_id'   => $recipe->id,
                'step_number' => $index + 1,
                'description' => $description,
            ]);
        }
    }
}
