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
    <form action="{{ route('recipes.store') }}" method="POST" enctype="multipart/form-data" class="recipe-form">
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

    <script>
        // 画像プレビュー
        document.getElementById('image').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.getElementById('image-preview');
                    preview.innerHTML = `<img src="${e.target.result}" style="width: 100%; height: 100%; object-fit: cover; border-radius: 8px;">`;
                }
                reader.readAsDataURL(file);
            }
        });

        // 材料追加
        let ingredientCount = 1;
        document.getElementById('addIngredientBtn').addEventListener('click', function() {
            const list = document.getElementById('ingredientList');
            const item = document.createElement('div');
            item.className = 'ingredient-item';
            item.innerHTML = `
                <input type="text" name="ingredients[${ingredientCount}][name]" placeholder="材料名" class="ingredient-input">
                <input type="text" name="ingredients[${ingredientCount}][quantity]" placeholder="分量" class="quantity-input">
                <button type="button" class="delete-btn" onclick="removeItem(this)">🗑</button>
            `;
            list.appendChild(item);
            ingredientCount++;
        });

        // 手順追加
        let stepCount = 1;
        document.getElementById('addStepBtn').addEventListener('click', function() {
            const list = document.getElementById('stepList');
            const item = document.createElement('div');
            item.className = 'step-item';
            item.innerHTML = `
                <span class="step-number">${stepCount + 1}</span>
                <input type="text" name="steps[${stepCount}][description]" placeholder="手順を入力" class="step-input">
                <button type="button" class="delete-btn" onclick="removeItem(this)">🗑</button>
            `;
            list.appendChild(item);
            stepCount++;
            updateStepNumbers();
        });

        // アイテム削除
        function removeItem(btn) {
            btn.parentElement.remove();
            updateStepNumbers();
        }

        // 手順番号を更新
        function updateStepNumbers() {
            const steps = document.querySelectorAll('.step-item');
            steps.forEach((step, index) => {
                step.querySelector('.step-number').textContent = index + 1;
            });
            stepCount = steps.length;
        }
    </script>
</body>
</html>