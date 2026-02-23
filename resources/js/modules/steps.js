// =============================
// 手順追加・削除・番号更新
// =============================
import { trashSVG, autoResize } from "./utils.js";

let stepCount;

export function initSteps(initialCount) {
    stepCount = initialCount;

    const addStepBtn = document.getElementById("addStepBtn");
    if (!addStepBtn) return;

    addStepBtn.addEventListener("click", function () {
        const list = document.getElementById("stepList");
        const item = document.createElement("div");
        item.className = "step-item";
        item.innerHTML = `
            <span class="step-number">${stepCount + 1}</span>
            <textarea name="steps[${stepCount}][description]" placeholder="手順を入力" class="step-input" rows="1"></textarea>
            <button type="button" class="delete-btn" onclick="removeItem(this)">${trashSVG}</button>
        `;
        item.querySelector("textarea").addEventListener("input", autoResize);
        list.appendChild(item);
        stepCount++;
        updateStepNumbers();
    });
}

export function updateStepNumbers() {
    const steps = document.querySelectorAll(".step-item");
    steps.forEach((step, index) => {
        step.querySelector(".step-number").textContent = index + 1;
    });
    stepCount = steps.length;
}
