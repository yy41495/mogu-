import { initImagePreview } from "./modules/image.js"; // 画像プレビュー
import { initIngredients } from "./modules/ingredients.js"; // 材料追加・削除
import { initSteps } from "./modules/steps.js"; // 手順追加・削除・番号更新
import { initTags, closeTagModal, showNewTagForm, hideNewTagForm, addNewTag, clearSelectedTags, applyTags, removeTag } from "./modules/tags.js"; // タグ選択・追加・削除
import { initAutoResize, removeItem, validateForm } from "./modules/utils.js"; // autoResize・validateForm など共通処理

// 初期カウント
const recipeForm = document.querySelector(".recipe-form");
const ingredientCount = recipeForm ? parseInt(recipeForm.dataset.ingredientCount) || 1 : 1;
const stepCount = recipeForm ? parseInt(recipeForm.dataset.stepCount) || 1 : 1;

document.addEventListener("DOMContentLoaded", function () {
    initImagePreview();
    initIngredients(ingredientCount);
    initSteps(stepCount);
    initTags();
    initAutoResize();

    // Enterキー送信防止（textareaの中は除く）
    if (recipeForm) {
        recipeForm.addEventListener("keydown", function (e) {
            if (e.key === "Enter" && e.target.tagName !== "TEXTAREA" && e.target.type !== "submit") {
                e.preventDefault();
            }
        });
    }
});

// =============================
// グローバル公開（HTMLのonclick属性から呼ばれる関数）
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
