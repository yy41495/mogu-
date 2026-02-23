<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <link rel="icon" href="/favicon.ico">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>mogu+ | レシピ追加</title>
    <script src="https://unpkg.com/lucide@latest"></script>
    @vite(['resources/css/common.css', 'resources/css/recipes.css', 'resources/css/form.css'])
</head>

<body>
    <!-- ヘッダー -->
    <div class="header">
        <div class="header-inner">
            <div class="header-left">
                <a href="{{ route('recipes.index') }}" class="back-link">
                    <i data-lucide="arrow-left"></i> 一覧に戻る
                </a>
            </div>
        </div>
        <div class="logo-text">レシピ追加</div>
    </div>

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
            <input type="url" id="source_url" name="source_url" placeholder="https://..." value="{{ old('source_url') }}">
        </div>

        <button type="submit" class="save-btn">保存</button>
    </form>

    <!-- タグ選択モーダル -->
    <div id="tagModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>タグを選択</h3>
                <button type="button" class="modal-close" onclick="closeTagModal()">
                    <i data-lucide="x"></i>
                </button>
            </div>
            <div class="modal-body">
                <input type="text" id="tagSearch" placeholder="タグを検索" class="tag-search">
                <div class="new-tag-area">
                    <button type="button" class="new-tag-btn" onclick="showNewTagForm()">+ 新規タグを追加</button>
                </div>
                <div id="newTagForm" class="new-tag-form hidden">
                    <input type="text" id="newTagName" placeholder="新規登録タグ名" class="new-tag-input">
                    <div class="color-picker">
                        <button type="button" class="color-btn color-btn--pink" data-color="#ffcdd2"></button>
                        <button type="button" class="color-btn color-btn--yellow" data-color="#fff9c4"></button>
                        <button type="button" class="color-btn color-btn--green" data-color="#c8e6c9"></button>
                        <button type="button" class="color-btn color-btn--blue" data-color="#bbdefb"></button>
                        <button type="button" class="color-btn color-btn--purple" data-color="#e1bee7"></button>
                        <button type="button" class="color-btn color-btn--brown" data-color="#d7ccc8"></button>
                        <button type="button" class="color-btn color-btn--gray" data-color="#e0e0e0"></button>
                    </div>
                    <div class="new-tag-actions">
                        <button type="button" class="cancel-btn" onclick="hideNewTagForm()">キャンセル</button>
                        <button type="button" class="add-tag-btn" onclick="addNewTag()">追加</button>
                    </div>
                </div>
                <div class="tag-section">
                    <h4>すべてのタグ</h4>
                    <div id="allTags" class="tag-list">
                        @foreach($tags as $tag)
                        <label class="tag-checkbox">
                            <input type="checkbox" value="{{ $tag->id }}" data-name="{{ $tag->name }}" data-color="{{ $tag->color ?? '#e0e0e0' }}">
                            <span class="tag-label" style="background-color: {{ $tag->color ?? '#e0e0e0' }};">{{ $tag->name }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="clear-btn" onclick="clearSelectedTags()">クリア</button>
                <button type="button" class="apply-btn" onclick="applyTags()">適用</button>
            </div>
        </div>
    </div>

    @vite(['resources/js/recipe-form.js'])
    <script>
        lucide.createIcons();
    </script>
</body>

</html>