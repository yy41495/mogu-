document.addEventListener('DOMContentLoaded', function() {
    const userIcon = document.getElementById('userIconBtn');
    const userMenu = document.getElementById('userMenu');

    if (userIcon && userMenu) {
        userIcon.addEventListener('click', function(e) {
            e.stopPropagation();
            userMenu.classList.toggle('hidden');
        });

        document.addEventListener('click', function(e) {
            if (!userIcon.contains(e.target) && !userMenu.contains(e.target)) {
                userMenu.classList.add('hidden');
            }
        });
    }
});

function toggleFilterModal() {
    document.getElementById('filterModal').classList.toggle('hidden');
}

function closeFilterModal() {
    document.getElementById('filterModal').classList.add('hidden');
}

function clearFilters() {
    const form = document.getElementById('searchForm');
    form.querySelectorAll('input[name="tags[]"]').forEach(cb => cb.checked = false);
    form.submit();
}

function clearTagFilters() {
    document.querySelectorAll('input[name="tags[]"]').forEach(cb => cb.checked = false);
}

window.toggleFilterModal = toggleFilterModal;
window.closeFilterModal = closeFilterModal;
window.clearFilters = clearFilters;
window.clearTagFilters = clearTagFilters;