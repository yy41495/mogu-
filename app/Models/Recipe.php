<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Recipe extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'title',
        'memo',
        'source_url',
        'image_path',
    ];

    protected $dates = ['deleted_at'];

    /**
     * このレシピを作成したユーザー
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * このレシピのタグ
     */
    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'recipe_tags');
    }

    /**
     * このレシピの材料
     */
    public function recipeIngredients()
    {
        return $this->hasMany(RecipeIngredient::class);
    }

    /**
     * このレシピの手順
     */
    public function steps()
    {
        return $this->hasMany(Step::class)->orderBy('step_number');
    }
}