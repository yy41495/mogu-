@extends('layouts.app')

@section('title', $recipe->title . ' | mogu+')

@section('css')
    @vite(['resources/css/recipe-detail.css'])
@endsection

{{-- ヘッダー左：戻るリンク --}}
@section('header-left')
    <a href="{{ route('recipes.index') }}" class="back-link">
        <i data-lucide="arrow-left"></i> 一覧に戻る
    </a>
@endsection

{{-- ヘッダー右：ロゴテキスト --}}
@section('header-right')
    <img src="/images/logo-text.png" alt="mogu+" class="logo-text-img">
@endsection

@section('content')
    <!-- 画面スリープ防止ボタン -->
    <button type="button" class="sleep-prevent-btn" id="sleepPreventBtn" title="画面スリープ防止">
        <i data-lucide="sun"></i>
    </button>

    <!-- メイン画像 -->
    @if($recipe->image_path)
    <div class="recipe-main-image">
        <img src="{{ $recipe->image_path }}" alt="{{ $recipe->title }}">
    </div>
    @endif

    <!-- レシピ情報 -->
    <div class="recipe-content">
        <h1 class="recipe-title">{{ $recipe->title }}</h1>

        @if($recipe->tags->count() > 0)
        <div class="recipe-tags">
            @foreach($recipe->tags as $tag)
            <span class="tag-badge" style="background-color: {{ $tag->color ?? '#e0e0e0' }};">
                {{ $tag->name }}
            </span>
            @endforeach
        </div>
        @endif

        @if($recipe->memo)
        <div class="recipe-section">
            <h2 class="section-title">自分用メモ</h2>
            <p class="recipe-memo">{{ $recipe->memo }}</p>
        </div>
        @endif

        @if($recipe->recipeIngredients->count() > 0)
        <div class="recipe-section">
            <h2 class="section-title">材料・分量</h2>
            <table class="ingredients-table">
                @foreach($recipe->recipeIngredients as $recipeIngredient)
                <tr>
                    <td class="ingredient-name">{{ $recipeIngredient->ingredient->name }}</td>
                    <td class="ingredient-quantity">{{ $recipeIngredient->quantity }}</td>
                </tr>
                @endforeach
            </table>
        </div>
        @endif

        @if($recipe->steps->count() > 0)
        <div class="recipe-section">
            <h2 class="section-title">手順</h2>
            <div class="steps-list">
                @foreach($recipe->steps as $step)
                <div class="step-item">
                    <span class="step-number">{{ $step->step_number }}</span>
                    <p class="step-description">{{ $step->description }}</p>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        @if($recipe->source_url)
        <div class="recipe-section">
            <h2 class="section-title">参考URL</h2>
            <a href="{{ $recipe->source_url }}" target="_blank" class="source-url">
                {{ $recipe->source_url }}
            </a>
        </div>
        @endif

        <!-- 編集・削除ボタン -->
        <div class="action-buttons">
            <a href="{{ route('recipes.edit', $recipe->id) }}" class="edit-btn">
                <i data-lucide="square-pen"></i> 編集
            </a>
            <button type="button" class="delete-btn" onclick="openDeleteModal()">
                <i data-lucide="trash-2"></i> 削除
            </button>
        </div>
    </div>

    <!-- 削除確認モーダル -->
    <div id="deleteModal" class="delete-modal hidden">
        <div class="delete-modal-content">
            <p class="delete-modal-title">本当に削除しますか？</p>
            <p class="delete-modal-desc">この操作は取り消せません</p>
            <div class="delete-modal-actions">
                <button type="button" class="delete-modal-cancel" onclick="closeDeleteModal()">キャンセル</button>
                <button type="button" class="delete-modal-confirm" onclick="submitDelete()">削除</button>
            </div>
        </div>
    </div>

    <form id="deleteForm" action="{{ route('recipes.destroy', $recipe->id) }}" method="POST" class="hidden">
        @csrf
        @method('DELETE')
    </form>
@endsection

@section('scripts')
    @vite(['resources/js/sleep.js', 'resources/js/delete.js'])
@endsection
