// =============================
// タグ選択・追加・削除
// =============================
let selectedTagIds = [];
let selectedColor = "#e0e0e0";

export function initTags() {
    // タグ選択モーダルを開く
    const tagBtn = document.getElementById("tagSelectBtn");
    if (tagBtn) {
        tagBtn.addEventListener("click", function () {
            document.getElementById("tagModal").style.display = "flex";
        });
    }

    // モーダル外クリックで閉じる
    const modal = document.getElementById("tagModal");
    if (modal) {
        modal.addEventListener("click", function (e) {
            if (e.target === this) closeTagModal();
        });
    }

    // 色選択
    document.querySelectorAll(".color-btn").forEach((btn) => {
        btn.addEventListener("click", function () {
            document.querySelectorAll(".color-btn").forEach((b) => b.classList.remove("selected"));
            this.classList.add("selected");
            selectedColor = this.dataset.color;
        });
    });

    // タグ検索
    const tagSearch = document.getElementById("tagSearch");
    if (tagSearch) {
        tagSearch.addEventListener("input", function (e) {
            const searchTerm = e.target.value.toLowerCase();
            document.querySelectorAll(".tag-checkbox").forEach((label) => {
                const tagName = label.querySelector("input").dataset.name.toLowerCase();
                label.style.display = tagName.includes(searchTerm) ? "flex" : "none";
            });
        });
    }
}

export function closeTagModal() {
    document.getElementById("tagModal").style.display = "none";
}

export function showNewTagForm() {
    document.getElementById("newTagForm").classList.remove("hidden");
}

export function hideNewTagForm() {
    document.getElementById("newTagForm").classList.add("hidden");
    document.getElementById("newTagName").value = "";
}

export function addNewTag() {
    const tagName = document.getElementById("newTagName").value.trim();
    if (!tagName) {
        alert("タグ名を入力してください");
        return;
    }

    const allTags = document.getElementById("allTags");
    const newId = "new_" + Date.now();

    const label = document.createElement("label");
    label.className = "tag-checkbox";
    label.innerHTML = `
        <input type="checkbox" value="${newId}" data-name="${tagName}" data-color="${selectedColor}" data-new="true" checked>
        <span class="tag-label" style="background-color:${selectedColor};">${tagName}</span>
    `;
    allTags.insertBefore(label, allTags.firstChild);

    selectedTagIds.push({ id: newId, name: tagName, color: selectedColor, isNew: true });
    hideNewTagForm();
}

export function clearSelectedTags() {
    document.querySelectorAll('#allTags input[type="checkbox"]').forEach((cb) => (cb.checked = false));
    selectedTagIds = [];
}

export function applyTags() {
    selectedTagIds = [];
    const selectedTagsDiv = document.getElementById("selectedTags");
    selectedTagsDiv.innerHTML = "";
    let newTagIndex = 0;

    document.querySelectorAll('#allTags input[type="checkbox"]:checked').forEach((cb) => {
        const tagData = {
            id: cb.value,
            name: cb.dataset.name,
            color: cb.dataset.color || "#e0e0e0",
            isNew: cb.dataset.new === "true",
        };
        selectedTagIds.push(tagData);

        const badge = document.createElement("span");
        badge.className = "tag-badge";
        badge.id = "tag_badge_" + tagData.id;
        badge.style.backgroundColor = tagData.color;
        badge.innerHTML = `${tagData.name}<span class="tag-remove" onclick="removeTag('${tagData.id}')">×</span>`;
        selectedTagsDiv.appendChild(badge);

        if (tagData.isNew) {
            const nameInput = document.createElement("input");
            nameInput.type = "hidden";
            nameInput.name = "new_tags[]";
            nameInput.value = tagData.name;
            selectedTagsDiv.appendChild(nameInput);

            const colorInput = document.createElement("input");
            colorInput.type = "hidden";
            colorInput.name = `new_tag_colors[${newTagIndex}]`;
            colorInput.value = tagData.color;
            selectedTagsDiv.appendChild(colorInput);
            newTagIndex++;
        } else {
            const input = document.createElement("input");
            input.type = "hidden";
            input.name = "tags[]";
            input.value = tagData.id;
            selectedTagsDiv.appendChild(input);
        }
    });

    closeTagModal();
}

export function removeTag(tagId) {
    const checkbox = document.querySelector(`#allTags input[value="${tagId}"]`);
    if (checkbox) checkbox.checked = false;
    selectedTagIds = selectedTagIds.filter((tag) => tag.id !== tagId);
    const badge = document.getElementById("tag_badge_" + tagId);
    if (badge) badge.remove();
}
