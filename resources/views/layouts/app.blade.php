<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <link rel="icon" href="/favicon.ico">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'mogu+')</title>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@400;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/css/base.css'])
    @yield('css')
    @vite(['resources/css/responsive.css'])
</head>

<body>
    <!-- ヘッダー -->
    <div class="header">
        <div class="header-inner">
            <div class="header-left">
                @yield('header-left')
            </div>
            @yield('header-right')
        </div>
    </div>

    <!-- ページ本文 -->
    @yield('content')

    @yield('scripts')
    <script>
        lucide.createIcons();
    </script>
</body>

</html>