// DOMが読み込まれてから実行
document.addEventListener('DOMContentLoaded', function() {
    const userIcon = document.querySelector('.user-icon');
    const userMenu = document.getElementById('userMenu');

    // ユーザーアイコンをクリック
    userIcon.addEventListener('click', function(e) {
        e.stopPropagation(); // イベントの伝播を止める
        const menu = document.getElementById('userMenu');
        menu.style.display = menu.style.display === 'none' ? 'block' : 'none';
    });

    // メニューの外をクリックしたら閉じる
    document.addEventListener('click', function(e) {
        if (!userIcon.contains(e.target) && !userMenu.contains(e.target)) {
            userMenu.style.display = 'none';
        }
    });
});

// フィルターモーダルの開閉
function toggleFilterModal() {
    const modal = document.getElementById('filterModal');
    modal.style.display = modal.style.display === 'none' ? 'flex' : 'none';
}

function closeFilterModal() {
    document.getElementById('filterModal').style.display = 'none';
}

// フィルタークリア
function clearFilters() {
    const form = document.getElementById('searchForm');
    const checkboxes = form.querySelectorAll('input[name="tags[]"]');
    checkboxes.forEach(cb => cb.checked = false);
    form.submit();
}

function clearTagFilters() {
    const checkboxes = document.querySelectorAll('input[name="tags[]"]');
    checkboxes.forEach(cb => cb.checked = false);
}

// グローバルに公開
window.toggleFilterModal = toggleFilterModal;
window.closeFilterModal = closeFilterModal;
window.clearFilters = clearFilters;
window.clearTagFilters = clearTagFilters;