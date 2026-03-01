<!-- タグ選択モーダル　create.blade.php と edit.blade.php で include して使う -->
<div id="tagModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>タグを選択</h3>
            <button type="button" class="modal-close" onclick="closeTagModal()">
                <i data-lucide="x"></i>
            </button>
        </div>
        <div class="modal-body">
            <input type="text" id="tagSearch" placeholder="タグを検索" class="tag-search">
            <div class="new-tag-area">
                <button type="button" class="new-tag-btn" onclick="showNewTagForm()">+ 新規タグを追加</button>
            </div>
            <div id="newTagForm" class="new-tag-form hidden">
                <input type="text" id="newTagName" placeholder="新規登録タグ名" class="new-tag-input">
                <div class="color-picker">
                    <button type="button" class="color-btn color-btn--pink"   data-color="#ffcdd2"></button>
                    <button type="button" class="color-btn color-btn--yellow" data-color="#fff9c4"></button>
                    <button type="button" class="color-btn color-btn--green"  data-color="#c8e6c9"></button>
                    <button type="button" class="color-btn color-btn--blue"   data-color="#bbdefb"></button>
                    <button type="button" class="color-btn color-btn--purple" data-color="#e1bee7"></button>
                    <button type="button" class="color-btn color-btn--brown"  data-color="#d7ccc8"></button>
                    <button type="button" class="color-btn color-btn--gray"   data-color="#e0e0e0"></button>
                </div>
                <div class="new-tag-actions">
                    <button type="button" class="cancel-btn" onclick="hideNewTagForm()">キャンセル</button>
                    <button type="button" class="add-tag-btn" onclick="addNewTag()">追加</button>
                </div>
            </div>
            <div class="tag-section">
                <h4>すべてのタグ</h4>
                <div id="allTags" class="tag-list">
                    @foreach($tags as $tag)
                    <label class="tag-checkbox">
                        <input type="checkbox"
                            value="{{ $tag->id }}"
                            data-name="{{ $tag->name }}"
                            data-color="{{ $tag->color ?? '#e0e0e0' }}"
                            {{ isset($recipe) && $recipe->tags->contains($tag->id) ? 'checked' : '' }}>
                        <span class="tag-label" style="background-color: {{ $tag->color ?? '#e0e0e0' }};">{{ $tag->name }}</span>
                    </label>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="clear-btn" onclick="clearSelectedTags()">クリア</button>
            <button type="button" class="apply-btn" onclick="applyTags()">適用</button>
        </div>
    </div>
</div>
