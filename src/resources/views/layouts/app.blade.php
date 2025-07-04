<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>Laravel ECサイト</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- Bootstrap（必要なら） --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <header class="container py-3 border-bottom d-flex justify-content-between align-items-center">
        <a href="{{ url('/') }}" class="d-flex align-items-center text-decoration-none">
            <img src="{{ asset('images/logo.svg') }}" alt="ロゴ" style="height: 40px;">
            <span class="ms-2 h5 mb-0 text-dark">ECサイト</span>
        </a>

        <div>
            @auth
                <form method="POST" action="{{ route('logout') }}" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-outline-secondary">ログアウト</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="btn btn-outline-primary me-2">ログイン</a>
                <a href="{{ route('register') }}" class="btn btn-primary">新規登録</a>
            @endauth
        </div>
    </header>

    <main class="container my-4">
        @yield('content')
    </main>

    {{-- Bootstrap JS（必要なら） --}}
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
