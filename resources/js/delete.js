function openDeleteModal() {
    document.getElementById('deleteModal').classList.remove('hidden');
    document.getElementById('deleteModal').style.display = 'flex';
}
function closeDeleteModal() {
    document.getElementById('deleteModal').classList.add('hidden');
    document.getElementById('deleteModal').style.display = '';
}
function submitDelete() {
    document.getElementById('deleteForm').submit();
}

document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('deleteModal').addEventListener('click', function(e) {
        if (e.target === this) closeDeleteModal();
    });
});

// グローバル公開（HTMLのonclick属性から呼ばれるため必須）
window.openDeleteModal = openDeleteModal;
window.closeDeleteModal = closeDeleteModal;
window.submitDelete = submitDelete;