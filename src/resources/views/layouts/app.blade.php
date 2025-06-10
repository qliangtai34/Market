<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Laravel ECサイト</title>
</head>
<body>
    <header>
        <h1><a href="{{ url('/') }}">ECサイト</a></h1>
        @auth
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit">ログアウト</button>
            </form>
        @else
            <a href="{{ route('login') }}">ログイン</a> |
            <a href="{{ route('register') }}">新規登録</a>
        @endauth
        <hr>
    </header>

    <main>
        @yield('content')
    </main>
</body>
</html>
