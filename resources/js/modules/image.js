// =============================
// 画像プレビュー ＋ 圧縮
// =============================

function compressImage(file, maxWidth = 1280, maxHeight = 1280) {
    return new Promise((resolve, reject) => {
        const reader = new FileReader();

        reader.onload = (event) => {
            const img = new Image();

            img.onload = () => {
                let width = img.width;
                let height = img.height;

                if (width > maxWidth || height > maxHeight) {
                    const ratio = Math.min(maxWidth / width, maxHeight / height);
                    width = Math.floor(width * ratio);
                    height = Math.floor(height * ratio);
                }

                const canvas = document.createElement("canvas");
                canvas.width = width;
                canvas.height = height;
                const ctx = canvas.getContext("2d");
                ctx.drawImage(img, 0, 0, width, height);

                const TARGET_SIZE = 0.5 * 1024 * 1024; // 500KB以下を目標に
                let quality = 0.85;

                const tryCompress = () => {
                    canvas.toBlob(
                        (blob) => {
                            if (!blob) {
                                reject(new Error("圧縮失敗"));
                                return;
                            }

                            if (blob.size <= TARGET_SIZE || quality <= 0.1) {
                                console.log(`元: ${(file.size / 1024).toFixed(0)}KB → 圧縮後: ${(blob.size / 1024).toFixed(0)}KB（画質${Math.round(quality * 100)}%）`);
                                resolve(blob);
                            } else {
                                quality = Math.round((quality - 0.1) * 10) / 10;
                                tryCompress();
                            }
                        },
                        "image/jpeg",
                        quality
                    );
                };

                tryCompress();
            };

            img.onerror = () => reject(new Error("画像読み込み失敗"));
            img.src = event.target.result;
        };

        reader.onerror = () => reject(new Error("ファイル読み込み失敗"));
        reader.readAsDataURL(file);
    });
}

export function initImagePreview() {
    const imageInput = document.getElementById("image");
    if (!imageInput) return;

    imageInput.addEventListener("change", async function (e) {
        const file = e.target.files[0];
        if (!file) return;

        const preview = document.getElementById("image-preview");
        preview.innerHTML = `<p style="text-align:center; padding:20px; color:#999;">圧縮中...</p>`;

        try {
            const compressedBlob = await compressImage(file);

            const previewUrl = URL.createObjectURL(compressedBlob);
            preview.innerHTML = `<img src="${previewUrl}"
                style="width:100%;height:100%;object-fit:cover;border-radius:8px;">`;

            const compressedFile = new File(
                [compressedBlob],
                file.name.replace(/\.[^.]+$/, ".jpg"),
                { type: "image/jpeg" }
            );

            const dataTransfer = new DataTransfer();
            dataTransfer.items.add(compressedFile);
            imageInput.files = dataTransfer.files;

        } catch (err) {
            preview.innerHTML = `<p style="text-align:center; color:red; padding:20px;">画像の容量が大きすぎます。<br>別の画像を選んでください。</p>`;
            console.error(err);
        }
    });
}