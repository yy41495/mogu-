<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <link rel="icon" href="/favicon.ico">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'mogu+')</title>
    @vite(['resources/css/base.css', 'resources/css/login.css'])
</head>
<body>
    @yield('content')
</body>
</html>
