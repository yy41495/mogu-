@extends('layouts.app')

@section('title', 'mogu+ | レシピ追加')

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
    <span class="logo-text">レシピ追加</span>
@endsection

@section('content')
<form action="{{ route('recipes.store') }}" method="POST" enctype="multipart/form-data" class="recipe-form" onsubmit="return validateForm(event)">
    @csrf

    <!-- 画像アップロード -->
    <div class="form-group">
        <label class="image-upload-area" for="image">
            <input type="file" id="image" name="image" accept="image/*" class="hidden-input">
            <div class="image-placeholder" id="image-preview">
                <i data-lucide="image" class="upload-icon-svg"></i>
                <span class="upload-text">タップして画像を選択</span>
            </div>
        </label>
    </div>

    <!-- タイトル -->
    <div class="form-group">
        <label for="title">タイトル <span class="required">*必須</span></label>
        <input type="text" id="title" name="title" placeholder="タイトルを入力" required value="{{ old('title') }}">
        @error('title')
        <span class="error-message">{{ $message }}</span>
        @enderror
    </div>

    <!-- タグ -->
    <div class="form-group">
        <label>タグ</label>
        <div class="tag-container">
            <button type="button" class="tag-select-btn" id="tagSelectBtn">タグを選択</button>
            <div id="selectedTags" class="selected-tags"></div>
        </div>
    </div>

    <!-- 材料・分量 -->
    <div class="form-group">
        <label>材料・分量</label>
        <div id="ingredientList" class="ingredient-list">
            <div class="ingredient-item">
                <input type="text" name="ingredients[0][name]" placeholder="材料名" class="ingredient-input">
                <input type="text" name="ingredients[0][quantity]" placeholder="分量" class="quantity-input">
                <button type="button" class="delete-btn" onclick="removeItem(this)">
                    <i data-lucide="trash-2"></i>
                </button>
            </div>
        </div>
        <button type="button" class="add-item-btn" id="addIngredientBtn">+ 材料を追加</button>
    </div>

    <!-- 手順 -->
    <div class="form-group">
        <label>手順</label>
        <div id="stepList" class="step-list">
            <div class="step-item">
                <span class="step-number">1</span>
                <textarea name="steps[0][description]" placeholder="手順を入力" class="step-input" rows="1"></textarea>
                <button type="button" class="delete-btn" onclick="removeItem(this)">
                    <i data-lucide="trash-2"></i>
                </button>
            </div>
        </div>
        <button type="button" class="add-item-btn" id="addStepBtn">+ 手順を追加</button>
    </div>

    <!-- 自分用メモ -->
    <div class="form-group">
        <label for="memo">自分用メモ</label>
        <textarea id="memo" name="memo" rows="4" placeholder="自分用メモを入力">{{ old('memo') }}</textarea>
    </div>

    <!-- 参考URL -->
    <div class="form-group">
        <label for="source_url">参考URL・引用元</label>
        <p>URLの場合、「取得」ボタンからタイトル・サムネイルを自動取得できます</p>
        <div class="url-input-group">
            <input type="url" id="source_url" name="source_url" placeholder="https://...">
            <button type="button" id="ogpFetchBtn" class="ogp-fetch-btn">取得</button>
        </div>
    </div>

    <button type="submit" class="save-btn">保存</button>
</form>

<!-- タグ選択モーダル -->
@include('recipes.partials.tag-modal')
@endsection

@section('scripts')
    @vite(['resources/js/recipe-form.js'])
@endsection
