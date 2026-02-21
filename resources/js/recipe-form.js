// 選択されたタグを保存する配列
let selectedTagIds = [];
let selectedColor = '#e0e0e0'; // デフォルトの色

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

// タグ選択ボタンクリック
document.getElementById('tagSelectBtn').addEventListener('click', function() {
    document.getElementById('tagModal').style.display = 'flex';
});

// モーダルを閉じる
function closeTagModal() {
    document.getElementById('tagModal').style.display = 'none';
}

// モーダルの外側をクリックしたら閉じる
document.getElementById('tagModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeTagModal();
    }
});

// 新規タグフォームを表示
function showNewTagForm() {
    document.getElementById('newTagForm').style.display = 'block';
}

// 新規タグフォームを非表示
function hideNewTagForm() {
    document.getElementById('newTagForm').style.display = 'none';
    document.getElementById('newTagName').value = '';
}

// 色選択
document.querySelectorAll('.color-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        document.querySelectorAll('.color-btn').forEach(b => b.classList.remove('selected'));
        this.classList.add('selected');
        selectedColor = this.dataset.color;
    });
});

// 新規タグを追加
function addNewTag() {
    const tagName = document.getElementById('newTagName').value.trim();
    if (!tagName) {
        alert('タグ名を入力してください');
        return;
    }

    // タグリストに追加
    const allTags = document.getElementById('allTags');
    const newId = 'new_' + Date.now(); // 仮のID
    
    const label = document.createElement('label');
    label.className = 'tag-checkbox';
    label.innerHTML = `
        <input type="checkbox" value="${newId}" data-name="${tagName}" data-color="${selectedColor}" data-new="true" checked>
        <span class="tag-label" style="background-color: ${selectedColor};">${tagName}</span>
    `;
    allTags.insertBefore(label, allTags.firstChild);

    // 選択状態に追加
    selectedTagIds.push({
        id: newId,
        name: tagName,
        color: selectedColor,
        isNew: true
    });

    hideNewTagForm();
}

// タグをクリア
function clearSelectedTags() {
    document.querySelectorAll('#allTags input[type="checkbox"]').forEach(cb => {
        cb.checked = false;
    });
    selectedTagIds = [];
}

// タグを適用
function applyTags() {
    selectedTagIds = [];
    const selectedTagsDiv = document.getElementById('selectedTags');
    selectedTagsDiv.innerHTML = '';

    let newTagIndex = 0;
    document.querySelectorAll('#allTags input[type="checkbox"]:checked').forEach(cb => {
        const tagData = {
            id: cb.value,
            name: cb.dataset.name,
            color: cb.dataset.color || '#e0e0e0',
            isNew: cb.dataset.new === 'true'
        };
        selectedTagIds.push(tagData);

        // 選択されたタグを表示
        const badge = document.createElement('span');
        badge.className = 'tag-badge';
        badge.style.backgroundColor = tagData.color;
        badge.innerHTML = `
            ${tagData.name}
            <span class="tag-remove" onclick="removeTag('${tagData.id}')">×</span>
        `;
        selectedTagsDiv.appendChild(badge);

        // hidden input を追加（フォーム送信用）
        if (tagData.isNew) {
            // 新規タグの場合、名前と色を別々に送信
            const nameInput = document.createElement('input');
            nameInput.type = 'hidden';
            nameInput.name = 'new_tags[]';
            nameInput.value = tagData.name;
            nameInput.id = 'tag_input_name_' + tagData.id;
            selectedTagsDiv.appendChild(nameInput);

            const colorInput = document.createElement('input');
            colorInput.type = 'hidden';
            colorInput.name = 'new_tag_colors[' + newTagIndex + ']';
            colorInput.value = tagData.color;
            colorInput.id = 'tag_input_color_' + tagData.id;
            selectedTagsDiv.appendChild(colorInput);

            newTagIndex++;
        } else {
            // 既存タグの場合
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'tags[]';
            input.value = tagData.id;
            input.id = 'tag_input_' + tagData.id;
            selectedTagsDiv.appendChild(input);
        }
    });

    closeTagModal();
}

// タグを削除
function removeTag(tagId) {
    // チェックボックスの選択を解除
    const checkbox = document.querySelector(`#allTags input[value="${tagId}"]`);
    if (checkbox) {
        checkbox.checked = false;
    }

    // 選択されたタグ配列から削除
    selectedTagIds = selectedTagIds.filter(tag => tag.id !== tagId);

    // 表示されているバッジと hidden input を削除
    const badge = event.target.parentElement;
    const nameInput = document.getElementById('tag_input_name_' + tagId);
    const colorInput = document.getElementById('tag_input_color_' + tagId);
    const normalInput = document.getElementById('tag_input_' + tagId);
    
    if (badge) badge.remove();
    if (nameInput) nameInput.remove();
    if (colorInput) colorInput.remove();
    if (normalInput) normalInput.remove();
}

// タグ検索
document.getElementById('tagSearch').addEventListener('input', function(e) {
    const searchTerm = e.target.value.toLowerCase();
    document.querySelectorAll('.tag-checkbox').forEach(label => {
        const tagName = label.querySelector('input').dataset.name.toLowerCase();
        label.style.display = tagName.includes(searchTerm) ? 'flex' : 'none';
    });
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

// フォームのバリデーション
function validateForm(event) {
    // タイトルが空ならエラー
    const title = document.getElementById('title').value.trim();
    if (!title) {
        alert('タイトルを入力してください');
        event.preventDefault();
        return false;
    }
    return true;
}

// Enterキーでフォーム送信されないようにする
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('.recipe-form');
    
    // フォーム全体でEnterキーを完全にブロック
    form.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' || e.keyCode === 13) {
            // submitボタン以外なら完全にキャンセル
            if (e.target.type !== 'submit' && e.target.tagName !== 'BUTTON') {
                e.preventDefault();
                e.stopPropagation();
                return false;
            }
        }
    });
    
    // さらに念押しでkeypressでもブロック
    form.addEventListener('keypress', function(e) {
        if (e.key === 'Enter' || e.keyCode === 13) {
            if (e.target.type !== 'submit' && e.target.tagName !== 'BUTTON') {
                e.preventDefault();
                e.stopPropagation();
                return false;
            }
        }
    });
});

// グローバルに公開（onclickで使えるように）
window.validateForm = validateForm;
window.closeTagModal = closeTagModal;
window.showNewTagForm = showNewTagForm;
window.hideNewTagForm = hideNewTagForm;
window.addNewTag = addNewTag;
window.clearSelectedTags = clearSelectedTags;
window.applyTags = applyTags;
window.removeTag = removeTag;
window.removeItem = removeItem;