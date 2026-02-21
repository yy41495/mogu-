<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>mogu+ | レシピ追加</title>
    @vite(['resources/css/common.css', 'resources/css/recipes.css', 'resources/css/form.css'])
</head>

<body>
    <!-- ヘッダー -->
    <div class="header">
        <a href="{{ route('recipes.index') }}" class="back-link">← 一覧に戻る</a>
        <div class="logo-text">レシピ追加</div>
        <div></div>
    </div>

    <!-- フォーム -->
    <form action="{{ route('recipes.store') }}" method="POST" enctype="multipart/form-data" class="recipe-form" onsubmit="return validateForm(event)">
        @csrf

        <!-- 画像アップロード -->
        <div class="form-group">
            <label class="image-upload-area" for="image">
                <input type="file" id="image" name="image" accept="image/*" style="display: none;">
                <div class="image-placeholder" id="image-preview">
                    <span class="upload-icon">📷</span>
                    <span class="upload-text">タップして画像を選択</span>
                </div>
            </label>
        </div>

        <!-- タイトル（必須） -->
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
            <button type="button" class="add-item-btn" id="addIngredientBtn">+ 材料を追加</button>
            <div id="ingredientList" class="ingredient-list">
                <div class="ingredient-item">
                    <input type="text" name="ingredients[0][name]" placeholder="材料名" class="ingredient-input">
                    <input type="text" name="ingredients[0][quantity]" placeholder="分量" class="quantity-input">
                    <button type="button" class="delete-btn" onclick="removeItem(this)">🗑</button>
                </div>
            </div>
        </div>

        <!-- 手順 -->
        <div class="form-group">
            <label>手順</label>
            <button type="button" class="add-item-btn" id="addStepBtn">+ 手順を追加</button>
            <div id="stepList" class="step-list">
                <div class="step-item">
                    <span class="step-number">1</span>
                    <input type="text" name="steps[0][description]" placeholder="手順を入力" class="step-input">
                    <button type="button" class="delete-btn" onclick="removeItem(this)">🗑</button>
                </div>
            </div>
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

        <!-- 保存ボタン -->
        <button type="submit" class="save-btn">保存</button>
    </form>

    <!-- タグ選択モーダル -->
    <div id="tagModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>タグを選択</h3>
                <button type="button" class="modal-close" onclick="closeTagModal()">×</button>
            </div>

            <div class="modal-body">
                <!-- タグ検索 -->
                <input type="text" id="tagSearch" placeholder="タグを検索" class="tag-search">

                <!-- 新規タグ追加 -->
                <div class="new-tag-area">
                    <button type="button" class="new-tag-btn" onclick="showNewTagForm()">+ 新規タグを追加</button>
                </div>

                <!-- 新規タグ登録フォーム（最初は非表示） -->
                <div id="newTagForm" class="new-tag-form" style="display: none;">
                    <input type="text" id="newTagName" placeholder="新規登録タグ名" class="new-tag-input">
                    <div class="color-picker">
                        <button type="button" class="color-btn" data-color="#ffcdd2" style="background-color: #ffcdd2;"></button>
                        <button type="button" class="color-btn" data-color="#fff9c4" style="background-color: #fff9c4;"></button>
                        <button type="button" class="color-btn" data-color="#c8e6c9" style="background-color: #c8e6c9;"></button>
                        <button type="button" class="color-btn" data-color="#bbdefb" style="background-color: #bbdefb;"></button>
                        <button type="button" class="color-btn" data-color="#e1bee7" style="background-color: #e1bee7;"></button>
                        <button type="button" class="color-btn" data-color="#d7ccc8" style="background-color: #d7ccc8;"></button>
                        <button type="button" class="color-btn" data-color="#e0e0e0" style="background-color: #e0e0e0;"></button>
                    </div>
                    <div class="new-tag-actions">
                        <button type="button" class="cancel-btn" onclick="hideNewTagForm()">キャンセル</button>
                        <button type="button" class="add-tag-btn" onclick="addNewTag()">追加</button>
                    </div>
                </div>

                <!-- すべてのタグ -->
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

</body>

</html>