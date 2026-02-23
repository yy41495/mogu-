// =============================
// 初期カウント
// =============================
const recipeForm = document.querySelector(".recipe-form");
let ingredientCount = recipeForm ? parseInt(recipeForm.dataset.ingredientCount) || 1 : 1;
let stepCount = recipeForm ? parseInt(recipeForm.dataset.stepCount) || 1 : 1;

// 選択されたタグを保存する配列
let selectedTagIds = [];
let selectedColor = "#e0e0e0"; // デフォルト色

// =============================
// ゴミ箱アイコンSVG（共通で使いまわす）
// =============================
const trashSVG = `
    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
        fill="none" stroke="white" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
        <polyline points="3 6 5 6 21 6"/>
        <path d="M19 6l-1 14H6L5 6"/>
        <path d="M10 11v6"/>
        <path d="M14 11v6"/>
        <path d="M9 6V4h6v2"/>
    </svg>
`;

document.addEventListener("DOMContentLoaded", function () {

    // =============================
    // 画像プレビュー
    // =============================
    const imageInput = document.getElementById("image");
    if (imageInput) {
        imageInput.addEventListener("change", function (e) {
            const file = e.target.files[0];
            if (!file) return;

            const reader = new FileReader();
            reader.onload = function (e) {
                const preview = document.getElementById("image-preview");
                preview.innerHTML = `<img src="${e.target.result}"
                    style="width:100%;height:100%;object-fit:cover;border-radius:8px;">`;
            };
            reader.readAsDataURL(file);
        });
    }

    // =============================
    // タグ選択ボタン
    // =============================
    const tagBtn = document.getElementById("tagSelectBtn");
    if (tagBtn) {
        tagBtn.addEventListener("click", function () {
            document.getElementById("tagModal").style.display = "flex";
        });
    }

    // モーダル外クリックで閉じる
    const modal = document.getElementById("tagModal");
    if (modal) {
        modal.addEventListener("click", function (e) {
            if (e.target === this) closeTagModal();
        });
    }

    // =============================
    // 色選択
    // =============================
    document.querySelectorAll(".color-btn").forEach((btn) => {
        btn.addEventListener("click", function () {
            document.querySelectorAll(".color-btn").forEach((b) => b.classList.remove("selected"));
            this.classList.add("selected");
            selectedColor = this.dataset.color;
        });
    });

    // =============================
    // タグ検索
    // =============================
    const tagSearch = document.getElementById("tagSearch");
    if (tagSearch) {
        tagSearch.addEventListener("input", function (e) {
            const searchTerm = e.target.value.toLowerCase();
            document.querySelectorAll(".tag-checkbox").forEach((label) => {
                const tagName = label.querySelector("input").dataset.name.toLowerCase();
                label.style.display = tagName.includes(searchTerm) ? "flex" : "none";
            });
        });
    }

    // =============================
    // 材料追加
    // =============================
    const addIngredientBtn = document.getElementById("addIngredientBtn");
    if (addIngredientBtn) {
        addIngredientBtn.addEventListener("click", function () {
            const list = document.getElementById("ingredientList");
            const item = document.createElement("div");
            item.className = "ingredient-item";
            item.innerHTML = `
                <input type="text" name="ingredients[${ingredientCount}][name]" placeholder="材料名" class="ingredient-input">
                <input type="text" name="ingredients[${ingredientCount}][quantity]" placeholder="分量" class="quantity-input">
                <button type="button" class="delete-btn" onclick="removeItem(this)">${trashSVG}</button>
            `;
            list.appendChild(item);
            ingredientCount++;
        });
    }

    // =============================
    // 手順追加
    // =============================
    const addStepBtn = document.getElementById("addStepBtn");
    if (addStepBtn) {
        addStepBtn.addEventListener("click", function () {
            const list = document.getElementById("stepList");
            const item = document.createElement("div");
            item.className = "step-item";
            item.innerHTML = `
                <span class="step-number">${stepCount + 1}</span>
                <textarea name="steps[${stepCount}][description]" placeholder="手順を入力" class="step-input" rows="1"></textarea>
                <button type="button" class="delete-btn" onclick="removeItem(this)">${trashSVG}</button>
            `;
            // 追加したtextareaに自動リサイズを設定
            item.querySelector("textarea").addEventListener("input", autoResize);
            list.appendChild(item);
            stepCount++;
            updateStepNumbers();
        });
    }

    // 最初からあるtextareaにも自動リサイズを設定
    document.querySelectorAll("textarea").forEach((el) => {
        el.addEventListener("input", autoResize);
    });

    // =============================
    // Enterキー送信防止
    // =============================
    const form = document.querySelector(".recipe-form");
    if (form) {
        form.addEventListener("keydown", function (e) {
            if (e.key === "Enter" && e.target.tagName !== "TEXTAREA" && e.target.type !== "submit") {
                e.preventDefault();
            }
        });
    }
});

// =============================
// テキストエリア自動リサイズ
// =============================
function autoResize() {
    this.style.height = "auto";
    this.style.height = this.scrollHeight + "px";
}

// =============================
// モーダル制御
// =============================
function closeTagModal() {
    document.getElementById("tagModal").style.display = "none";
}

function showNewTagForm() {
    document.getElementById("newTagForm").classList.remove("hidden");
}

function hideNewTagForm() {
    document.getElementById("newTagForm").classList.add("hidden");
    document.getElementById("newTagName").value = "";
}

// =============================
// タグ操作
// =============================
function addNewTag() {
    const tagName = document.getElementById("newTagName").value.trim();
    if (!tagName) {
        alert("タグ名を入力してください");
        return;
    }

    const allTags = document.getElementById("allTags");
    const newId = "new_" + Date.now();

    const label = document.createElement("label");
    label.className = "tag-checkbox";
    label.innerHTML = `
        <input type="checkbox" value="${newId}" data-name="${tagName}" data-color="${selectedColor}" data-new="true" checked>
        <span class="tag-label" style="background-color:${selectedColor};">${tagName}</span>
    `;
    allTags.insertBefore(label, allTags.firstChild);

    selectedTagIds.push({ id: newId, name: tagName, color: selectedColor, isNew: true });
    hideNewTagForm();
}

function clearSelectedTags() {
    document.querySelectorAll('#allTags input[type="checkbox"]').forEach((cb) => (cb.checked = false));
    selectedTagIds = [];
}

function applyTags() {
    selectedTagIds = [];
    const selectedTagsDiv = document.getElementById("selectedTags");
    selectedTagsDiv.innerHTML = "";
    let newTagIndex = 0;

    document.querySelectorAll('#allTags input[type="checkbox"]:checked').forEach((cb) => {
        const tagData = {
            id: cb.value,
            name: cb.dataset.name,
            color: cb.dataset.color || "#e0e0e0",
            isNew: cb.dataset.new === "true",
        };
        selectedTagIds.push(tagData);

        const badge = document.createElement("span");
        badge.className = "tag-badge";
        badge.id = "tag_badge_" + tagData.id;
        badge.style.backgroundColor = tagData.color;
        badge.innerHTML = `${tagData.name}<span class="tag-remove" onclick="removeTag('${tagData.id}')">×</span>`;
        selectedTagsDiv.appendChild(badge);

        if (tagData.isNew) {
            const nameInput = document.createElement("input");
            nameInput.type = "hidden";
            nameInput.name = "new_tags[]";
            nameInput.value = tagData.name;
            selectedTagsDiv.appendChild(nameInput);

            const colorInput = document.createElement("input");
            colorInput.type = "hidden";
            colorInput.name = `new_tag_colors[${newTagIndex}]`;
            colorInput.value = tagData.color;
            selectedTagsDiv.appendChild(colorInput);
            newTagIndex++;
        } else {
            const input = document.createElement("input");
            input.type = "hidden";
            input.name = "tags[]";
            input.value = tagData.id;
            selectedTagsDiv.appendChild(input);
        }
    });

    closeTagModal();
}

function removeTag(tagId) {
    const checkbox = document.querySelector(`#allTags input[value="${tagId}"]`);
    if (checkbox) checkbox.checked = false;
    selectedTagIds = selectedTagIds.filter((tag) => tag.id !== tagId);
    const badge = document.getElementById("tag_badge_" + tagId);
    if (badge) badge.remove();
}

// =============================
// 共通処理
// =============================
function removeItem(btn) {
    btn.parentElement.remove();
    updateStepNumbers();
}

function updateStepNumbers() {
    const steps = document.querySelectorAll(".step-item");
    steps.forEach((step, index) => {
        step.querySelector(".step-number").textContent = index + 1;
    });
    stepCount = steps.length;
}

function validateForm(event) {
    const title = document.getElementById("title").value.trim();
    if (!title) {
        alert("タイトルを入力してください");
        event.preventDefault();
        return false;
    }
    return true;
}

// =============================
// グローバル公開
// =============================
window.validateForm = validateForm;
window.closeTagModal = closeTagModal;
window.showNewTagForm = showNewTagForm;
window.hideNewTagForm = hideNewTagForm;
window.addNewTag = addNewTag;
window.clearSelectedTags = clearSelectedTags;
window.applyTags = applyTags;
window.removeTag = removeTag;
window.removeItem = removeItem;