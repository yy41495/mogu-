<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>mogu+ | レシピ一覧</title>
    @vite(['resources/css/common.css', 'resources/css/recipes.css'])
</head>
<body>
    <!-- ヘッダー -->
    <div class="header">
        <div class="header-left">
            <div class="logo-icon">ロゴ</div>
            <div class="logo-text">mogu+</div>
        </div>
        <div class="user-icon"></div>
    </div>

    <!-- 検索バー -->
    <div class="search-bar">
        <div class="search-container">
            <span class="search-icon">🔍</span>
            <input type="text" class="search-input" placeholder="キーワードで検索（タイトル・材料・タグ）">
            <span class="filter-icon">⚙</span>
        </div>
    </div>

    <!-- レシピ一覧 または 空の状態 -->
    @if($recipes->count() > 0)
        <div class="recipe-grid">
            @foreach($recipes as $recipe)
                <div class="recipe-card">
                    @if($recipe->image_path)
                        <img src="{{ asset('storage/' . $recipe->image_path) }}" alt="{{ $recipe->title }}" class="recipe-image">
                    @else
                        <div class="no-image">画像なし</div>
                    @endif
                </div>
            @endforeach
        </div>
    @else
        <div class="empty-state">
            <div class="empty-state-icon">📝</div>
            <div class="empty-state-text">レシピがありません</div>
            <div class="empty-state-subtext">右下の「+」ボタンから追加しましょう</div>
        </div>
    @endif

    <!-- 追加ボタン -->
    <a href="#" class="add-button">+</a>
</body>
</html>