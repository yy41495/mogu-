@extends('layouts.app')

@section('title', 'mogu+ | レシピ一覧')

@section('css')
@vite(['resources/css/recipes.css'])
@endsection

{{-- ヘッダー左：ロゴ画像 --}}
@section('header-left')
<img src="/images/logo-icon.png" alt="mogu+" class="logo-icon-img">
<img src="/images/logo-text.png" alt="mogu+" class="logo-text-img">
@endsection

{{-- ヘッダー右：ユーザーアイコン --}}
@section('header-right')
<div class="user-menu-wrapper">
    <button type="button" class="user-icon-btn" id="userIconBtn">
        <i data-lucide="circle-user-round"></i>
    </button>
    <div id="userMenu" class="user-menu hidden">
        <div class="user-name">{{ Auth::user()->name }}</div>
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="logout-btn">ログアウト</button>
        </form>
    </div>
</div>
@endsection

@section('content')
<main>
    <!-- 検索バー -->
    <div class="search-bar">
        <form action="{{ route('recipes.index') }}" method="GET" id="searchForm">
            <div class="search-container">
                <i data-lucide="search" class="search-icon"></i>
                <input type="text" name="keyword" class="search-input" placeholder="キーワードで検索（タイトル・材料・タグ）" value="{{ request('keyword') }}">
                <button type="button" class="filter-btn" onclick="toggleFilterModal()">
                    <i data-lucide="sliders-horizontal"></i>
                </button>
            </div>

            <!-- 選択中のタグ表示 -->
            @if(request('tags'))
            <div class="selected-filters">
                @foreach($allTags->whereIn('id', request('tags')) as $tag)
                <span class="filter-badge" style="background-color: {{ $tag->color ?? '#e0e0e0' }};">
                    {{ $tag->name }}
                </span>
                @endforeach
                <button type="button" class="clear-filters" onclick="clearFilters()">クリア</button>
            </div>
            @endif

            <!-- タグフィルターモーダル -->
            <div id="filterModal" class="modal hidden">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3>タグでフィルター</h3>
                        <button type="button" class="modal-close" onclick="closeFilterModal()">
                            <i data-lucide="x"></i>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="filter-tag-list">
                            @foreach($allTags as $tag)
                            <label class="filter-tag-checkbox">
                                <input type="checkbox" name="tags[]" value="{{ $tag->id }}" {{ in_array($tag->id, request('tags', [])) ? 'checked' : '' }}>
                                <span class="filter-tag-label" style="background-color: {{ $tag->color ?? '#e0e0e0' }};">{{ $tag->name }}</span>
                            </label>
                            @endforeach
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="clear-btn" onclick="clearTagFilters()">クリア</button>
                        <button type="submit" class="apply-btn">適用</button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- レシピ一覧 または 空の状態 -->
    @if($recipes->count() > 0)
    <div class="recipe-grid">
        @foreach($recipes as $recipe)
        <a href="{{ route('recipes.show', $recipe->id) }}" class="recipe-card">
            @if($recipe->image_path)
            <img src="{{ $recipe->image_path }}" alt="{{ $recipe->title }}" class="recipe-image">
            @else
            <div class="no-image">{{ $recipe->title }}</div>
            @endif
        </a>
        @endforeach
    </div>
    @else
    <div class="empty-state">
        <div class="empty-state-text">レシピがありません</div>
        <div class="empty-state-subtext">下の「+」ボタンから追加しましょう</div>
    </div>
    @endif

    <!-- ページネーション -->
    <div class="pagination-wrapper">
        {{ $recipes->links() }}
    </div>

    <!-- 追加ボタン -->
    <a href="{{ route('recipes.create') }}" class="add-button">
        <i data-lucide="plus"></i>
    </a>
</main>
@endsection

@section('scripts')
@vite(['resources/js/index.js'])
@endsection