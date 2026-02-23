// =============================
// 画像プレビュー
// =============================
export function initImagePreview() {
    const imageInput = document.getElementById("image");
    if (!imageInput) return;

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
