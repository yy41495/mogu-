<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $recipe->title }} | mogu+</title>
    @vite(['resources/css/common.css', 'resources/css/recipes.css', 'resources/css/recipe-detail.css'])
</head>

<body>
    <!-- ヘッダー -->
    <div class="header">
        <a href="{{ route('recipes.index') }}" class="back-link">← 一覧に戻る</a>
        <div class="logo-text">mogu+</div>
        <div></div>
    </div>

    <!-- 画面スリープ防止ボタン（右上固定） -->
    <button type="button" class="sleep-prevent-btn" id="sleepPreventBtn" title="画面スリープ防止">
        ☀️
    </button>

    <!-- メイン画像 -->
    @if($recipe->image_path)
    <div class="recipe-main-image">
        <img src="{{ $recipe->image_path }}" alt="{{ $recipe->title }}">
    </div>
    @endif

    <!-- レシピ情報 -->
    <div class="recipe-content">
        <!-- タイトル -->
        <h1 class="recipe-title">{{ $recipe->title }}</h1>

        <!-- タグ -->
        @if($recipe->tags->count() > 0)
        <div class="recipe-tags">
            @foreach($recipe->tags as $tag)
            <span class="tag-badge" style="background-color: {{ $tag->color ?? '#e0e0e0' }};">
                {{ $tag->name }}
            </span>
            @endforeach
        </div>
        @endif

        <!-- 自分用メモ -->
        @if($recipe->memo)
        <div class="recipe-section">
            <h2 class="section-title">自分用メモ</h2>
            <p class="recipe-memo">{{ $recipe->memo }}</p>
        </div>
        @endif

        <!-- 材料・分量 -->
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

        <!-- 手順 -->
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

        <!-- 参考URL -->
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
                ✏️ 編集
            </a>
            <form action="{{ route('recipes.destroy', $recipe->id) }}" method="POST" onsubmit="return confirm('本当に削除しますか？');" style="display: inline;">
                @csrf
                @method('DELETE')
                <button type="submit" class="delete-btn">
                    🗑 削除
                </button>
            </form>
        </div>
    </div>

    @vite(['resources/js/sleep.js'])
</body>

</html>