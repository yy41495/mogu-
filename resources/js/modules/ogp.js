// modules/ogp.js - OGP情報自動取得

export function initOgp() {
    const fetchBtn = document.getElementById('ogpFetchBtn');
    if (!fetchBtn) return;

    fetchBtn.addEventListener('click', async function () {
        const urlInput = document.getElementById('source_url');
        const url = urlInput.value.trim();

        // URLが空だったら何もしない
        if (!url) {
            alert('URLを入力してください');
            return;
        }

        // ボタンをローディング状態にする
        fetchBtn.textContent = '取得中...';
        fetchBtn.disabled = true;

        try {
            const response = await fetch('/ogp/fetch', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: JSON.stringify({ url }),
            });

            const data = await response.json();

            if (!response.ok) {
                alert(data.error || '取得に失敗しました');
                return;
            }

            // タイトルを自動入力（すでに入力されていたら確認する）
            if (data.title) {
                const titleInput = document.getElementById('title');
                if (titleInput.value && titleInput.value !== data.title) {
                    if (confirm('タイトルを「' + data.title + '」に上書きしますか？')) {
                        titleInput.value = data.title;
                    }
                } else {
                    titleInput.value = data.title;
                }
            }

            // 画像を自動入力（すでに画像があったら確認する）
            if (data.image) {
                const imagePreview = document.getElementById('image-preview');
                const hasImage = imagePreview.querySelector('img');
                if (hasImage) {
                    if (!confirm('画像を上書きしますか？')) {
                        return;
                    }
                }
                // OGP画像のURLをプレビュー表示
                imagePreview.innerHTML = '<img src="' + data.image + '" class="preview-img">';
                // hiddenフィールドにOGP画像URLを保存（Controller側で使う）
                let ogpImageInput = document.getElementById('ogp_image_url');
                if (!ogpImageInput) {
                    ogpImageInput = document.createElement('input');
                    ogpImageInput.type = 'hidden';
                    ogpImageInput.id = 'ogp_image_url';
                    ogpImageInput.name = 'ogp_image_url';
                    document.querySelector('.recipe-form').appendChild(ogpImageInput);
                }
                ogpImageInput.value = data.image;
            }

        } catch (error) {
            alert('通信エラーが発生しました');
        } finally {
            // ボタンを元に戻す
            fetchBtn.textContent = '取得';
            fetchBtn.disabled = false;
        }
    });
}