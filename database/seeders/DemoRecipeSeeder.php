<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Recipe;
use App\Models\Tag;
use App\Models\Ingredient;
use App\Models\Step;
use App\Models\RecipeIngredient;

class DemoRecipeSeeder extends Seeder
{
    public function run(): void
    {
        // ① 最初のユーザーを取得（いなければ終了）
        $user = User::first();
        if (!$user) {
            $this->command->info('ユーザーがいないためスキップしました。');
            return;
        }

        // ② タグを作成
        $tag = Tag::firstOrCreate(['name' => 'サンプル']);

        // ③ レシピを作成
        $recipe = Recipe::create([
            'user_id'    => $user->id,
            'title'      => 'README（サンプルレシピ）',
            'memo'       => "これはサンプルデータです。自由に削除してOKです！\n\nこのアプリでは、タイトル・材料・タグでレシピを検索できます。",
        ]);

        // ④ タグをレシピに紐付け
        $recipe->tags()->attach($tag->id);

        // ⑤ 材料を追加
        $ingredients = [
            ['name' => '好きな食材', 'quantity' => '適量'],
            ['name' => 'お気に入りの調味料', 'quantity' => '少々'],
        ];

        foreach ($ingredients as $item) {
            $ingredient = Ingredient::firstOrCreate(['name' => $item['name']]);
            RecipeIngredient::create([
                'recipe_id'   => $recipe->id,
                'ingredient_id' => $ingredient->id,
                'quantity'    => $item['quantity'],
            ]);
        }

        // ⑥ 手順を追加
        $steps = [
            'サムネイル一覧ページの＋ボタンからレシピを追加できます',
            '検索バーでタイトル・材料・タグを検索できます',
            '右上の太陽アイコンのボタンは画面スリープ防止ボタンです',
            'このサンプルレシピは削除してOKです！',
        ];

        foreach ($steps as $index => $description) {
            Step::create([
                'recipe_id'   => $recipe->id,
                'step_number' => $index + 1,
                'description' => $description,
            ]);
        }

        $this->command->info('サンプルレシピを作成しました！');
    }
}
