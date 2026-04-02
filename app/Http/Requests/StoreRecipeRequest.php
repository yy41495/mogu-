<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreRecipeRequest extends FormRequest
{
    // ログインしているユーザーだけ許可
    public function authorize(): bool
    {
        return Auth::check();
    }

    public function rules(): array
    {
        return [
            'title'            => 'required|max:255',
            'memo'             => 'nullable',
            'source_url'       => 'nullable|string|max:255',
            'image'            => 'nullable|image|max:5120',
            'ogp_image_url'    => 'nullable|url',
            'tags'             => 'nullable|array',
            'new_tags'         => 'nullable|array',
            'new_tags.*'       => 'nullable|string|max:50',
            'new_tag_colors'   => 'nullable|array',
            'new_tag_colors.*' => 'nullable|string|max:7',
            'ingredients'      => 'nullable|array',
            'steps'            => 'nullable|array',
        ];
    }
}