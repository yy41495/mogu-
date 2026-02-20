<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Recipe extends Model //=recipesテーブルを操作できるようにします
{
    use HasFactory, SoftDeletes;

    protected $fillable = [ //$fillableは一括代入の設定のための特別なプロパティ名
        'user_id',
        'title',
        'memo',
        'source_url',
        'image_path',
    ];

    protected $dates = ['deleted_at']; //=deleted_at を日付型として扱う

    //以下リレーション記述
    public function user()
    {
        return $this->belongsTo(User::class); //このレシピは1人のユーザーに属する
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'recipe_tags'); //レシピとタグは多対多
    }

    public function recipeIngredients()
    {
        return $this->hasMany(RecipeIngredient::class); //このレシピは複数の材料を持つ
    }

    public function steps()
    {
        return $this->hasMany(Step::class)->orderBy('step_number'); //このレシピは複数の手順を持つ、手順は番号順に並べる
    }
}