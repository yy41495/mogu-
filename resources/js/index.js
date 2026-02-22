document.addEventListener('DOMContentLoaded', function() {
    const userIcon = document.getElementById('userIconBtn');
    const userMenu = document.getElementById('userMenu');

    // ユーザーアイコンをクリック
    userIcon.addEventListener('click', function(e) {
        e.stopPropagation();
        userMenu.classList.toggle('hidden');
    });

    // メニューの外をクリックしたら閉じる
    document.addEventListener('click', function(e) {
        if (!userIcon.contains(e.target) && !userMenu.contains(e.target)) {
            userMenu.classList.add('hidden');
        }
    });
});

// フィルターモーダルの開閉（hiddenクラスだけで制御）
function toggleFilterModal() {
    document.getElementById('filterModal').classList.toggle('hidden');
}

function closeFilterModal() {
    document.getElementById('filterModal').classList.add('hidden');
}

// フィルタークリア
function clearFilters() {
    const form = document.getElementById('searchForm');
    form.querySelectorAll('input[name="tags[]"]').forEach(cb => cb.checked = false);
    form.submit();
}

function clearTagFilters() {
    document.querySelectorAll('input[name="tags[]"]').forEach(cb => cb.checked = false);
}

// グローバルに公開
window.toggleFilterModal = toggleFilterModal;
window.closeFilterModal = closeFilterModal;
window.clearFilters = clearFilters;
window.clearTagFilters = clearTagFilters;