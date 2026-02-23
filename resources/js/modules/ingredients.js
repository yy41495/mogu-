// =============================
// 材料追加・削除
// =============================
import { trashSVG } from "./utils.js";

let ingredientCount;

export function initIngredients(initialCount) {
    ingredientCount = initialCount;

    const addIngredientBtn = document.getElementById("addIngredientBtn");
    if (!addIngredientBtn) return;

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
