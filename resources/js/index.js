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