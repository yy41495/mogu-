// =============================
// 共通で使うもの
// =============================

// ゴミ箱アイコンSVG
export const trashSVG = `
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
        fill="none" stroke="white" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
        <polyline points="3 6 5 6 21 6"/>
        <path d="M19 6l-1 14H6L5 6"/>
        <path d="M10 11v6"/>
        <path d="M14 11v6"/>
        <path d="M9 6V4h6v2"/>
    </svg>
`;

// テキストエリア自動リサイズ
export function autoResize() {
    this.style.height = "auto";
    this.style.height = this.scrollHeight + "px";
}

// 既存のtextareaに自動リサイズを設定する
export function initAutoResize() {
    document.querySelectorAll("textarea").forEach((el) => {
        el.addEventListener("input", autoResize);
    });
}

// 材料・手順の行を削除する
// 削除後に手順番号も更新する（手順以外のリストでは番号要素がないので何も起きない）
export function removeItem(btn) {
    btn.parentElement.remove();
    const steps = document.querySelectorAll(".step-item");
    steps.forEach((step, index) => {
        step.querySelector(".step-number").textContent = index + 1;
    });
}

// フォームバリデーション
export function validateForm(event) {
    const title = document.getElementById("title").value.trim();
    if (!title) {
        alert("タイトルを入力してください");
        event.preventDefault();
        return false;
    }
    return true;
}
