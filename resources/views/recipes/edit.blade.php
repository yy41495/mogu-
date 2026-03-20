@extends('layouts.app')

@section('title', 'mogu+ | レシピ編集')

@section('css')
@vite(['resources/css/recipe-form.css'])
@endsection

<!-- ヘッダー左：戻るリンク -->
@section('header-left')
<a href="{{ route('recipes.index') }}" class="back-link">
    <i data-lucide="arrow-left"></i> 一覧に戻る
</a>
@endsection

<!-- ヘッダー右：ページタイトル -->
@section('header-right')
<span class="logo-text">レシピ編集</span>
@endsection

@section('content')
<form action="{{ route('recipes.update', $recipe->id) }}" method="POST" enctype="multipart/form-data"
    class="recipe-form" onsubmit="return validateForm(event)"
    data-ingredient-count="{{ $recipe->recipeIngredients->count() > 0 ? $recipe->recipeIngredients->count() : 1 }}"
    data-step-count="{{ $recipe->steps->count() > 0 ? $recipe->steps->count() : 1 }}">
    @csrf
    @method('PUT')

    <!-- 画像アップロード -->
    <div class="form-group">
        <label class="image-upload-area" for="image">
            <input type="file" id="image" name="image" accept="image/*" class="hidden-input">
            <div class="image-placeholder" id="image-preview">
                @if($recipe->image_path)
                <img src="{{ $recipe->image_path }}" class="preview-img">
                @else
                <i data-lucide="image" class="upload-icon-svg"></i>
                <span class="upload-text">タップして画像を選択</span>
                @endif
            </div>
        </label>
    </div>

    <!-- タイトル -->
    <div class="form-group">
        <label for="title">タイトル <span class="required">*必須</span></label>
        <input type="text" id="title" name="title" placeholder="タイトルを入力" required value="{{ old('title', $recipe->title) }}">
        @error('title')
        <span class="error-message">{{ $message }}</span>
        @enderror
    </div>

    <!-- タグ -->
    <div class="form-group">
        <label>タグ</label>
        <div class="tag-container">
            <button type="button" class="tag-select-btn" id="tagSelectBtn">タグを選択</button>
            <div id="selectedTags" class="selected-tags">
                @foreach($recipe->tags as $tag)
                <span class="tag-badge" id="tag_badge_{{ $tag->id }}" style="background-color: {{ $tag->color ?? '#e0e0e0' }};">
                    {{ $tag->name }}
                    <span class="tag-remove" onclick="removeTag('{{ $tag->id }}')">×</span>
                </span>
                <input type="hidden" name="tags[]" value="{{ $tag->id }}" id="tag_input_{{ $tag->id }}">
                @endforeach
            </div>
        </div>
    </div>

    <!-- 材料・分量 -->
    <div class="form-group">
        <label>材料・分量</label>
        <div id="ingredientList" class="ingredient-list">
            @if($recipe->recipeIngredients->count() > 0)
            @foreach($recipe->recipeIngredients as $index => $recipeIngredient)
            <div class="ingredient-item">
                <input type="text" name="ingredients[{{ $index }}][name]" placeholder="材料名" class="ingredient-input" value="{{ $recipeIngredient->ingredient->name }}">
                <input type="text" name="ingredients[{{ $index }}][quantity]" placeholder="分量" class="quantity-input" value="{{ $recipeIngredient->quantity }}">
                <button type="button" class="delete-btn" onclick="removeItem(this)">
                    <i data-lucide="trash-2"></i>
                </button>
            </div>
            @endforeach
            @else
            <div class="ingredient-item">
                <input type="text" name="ingredients[0][name]" placeholder="材料名" class="ingredient-input">
                <input type="text" name="ingredients[0][quantity]" placeholder="分量" class="quantity-input">
                <button type="button" class="delete-btn" onclick="removeItem(this)">
                    <i data-lucide="trash-2"></i>
                </button>
            </div>
            @endif
        </div>
        <button type="button" class="add-item-btn" id="addIngredientBtn">+ 材料を追加</button>
    </div>

    <!-- 手順 -->
    <div class="form-group">
        <label>手順</label>
        <div id="stepList" class="step-list">
            @if($recipe->steps->count() > 0)
            @foreach($recipe->steps as $index => $step)
            <div class="step-item">
                <span class="step-number">{{ $step->step_number }}</span>
                <textarea name="steps[{{ $index }}][description]" placeholder="手順を入力" class="step-input" rows="1">{{ $step->description }}</textarea>
                <button type="button" class="delete-btn" onclick="removeItem(this)">
                    <i data-lucide="trash-2"></i>
                </button>
            </div>
            @endforeach
            @else
            <div class="step-item">
                <span class="step-number">1</span>
                <textarea name="steps[0][description]" placeholder="手順を入力" class="step-input" rows="1"></textarea>
                <button type="button" class="delete-btn" onclick="removeItem(this)">
                    <i data-lucide="trash-2"></i>
                </button>
            </div>
            @endif
        </div>
        <button type="button" class="add-item-btn" id="addStepBtn">+ 手順を追加</button>
    </div>

    <!-- 自分用メモ -->
    <div class="form-group">
        <label for="memo">自分用メモ</label>
        <textarea id="memo" name="memo" rows="4" placeholder="自分用メモを入力">{{ old('memo', $recipe->memo) }}</textarea>
    </div>

    <!-- 参考URL -->
    <div class="form-group">
        <label for="source_url">参考URL・引用元</label>
        <p>URLの場合、「取得」ボタンからタイトル・サムネイルを自動取得できます</p>
        <div class="url-input-group">
            <input type="text" id="source_url" name="source_url" placeholder="https://...">
            <button type="button" id="ogpFetchBtn" class="ogp-fetch-btn">取得</button>
        </div>
    </div>

    <button type="submit" class="save-btn">更新</button>
</form>

<!-- タグ選択モーダル -->
@include('recipes.partials.tag-modal')
@endsection

@section('scripts')
@vite(['resources/js/recipe-form.js'])
@endsection