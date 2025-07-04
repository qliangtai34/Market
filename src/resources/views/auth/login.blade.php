@extends('layouts.app')

@section('content')
<div class="container" style="max-width: 480px;">
    <h2 class="mb-4">ログイン</h2>

    {{-- ログイン失敗 or フォーマットエラー時 --}}
    @if ($errors->has('auth') || ($errors->has('email') && $errors->first('email') !== 'メールアドレスを入力してください') || ($errors->has('password') && $errors->first('password') !== 'パスワードを入力してください'))
        <div class="alert alert-danger">
            ログイン情報が登録されていません。
        </div>
    @endif

    <form method="POST" action="{{ route('login.attempt') }}">
        @csrf

        {{-- メールアドレス --}}
        <div class="mb-3">
            <label for="email" class="form-label">メールアドレス</label>
            <input type="text" name="email" id="email"
                   value="{{ old('email') }}"
                   class="form-control @if($errors->first('email') === 'メールアドレスを入力してください') is-invalid @endif">

            @if ($errors->first('email') === 'メールアドレスを入力してください')
                <div class="invalid-feedback">{{ $errors->first('email') }}</div>
            @endif
        </div>

        {{-- パスワード --}}
        <div class="mb-3">
            <label for="password" class="form-label">パスワード</label>
            <input type="password" name="password" id="password"
                   class="form-control @if($errors->first('password') === 'パスワードを入力してください') is-invalid @endif">

            @if ($errors->first('password') === 'パスワードを入力してください')
                <div class="invalid-feedback">{{ $errors->first('password') }}</div>
            @endif
        </div>

        <button type="submit" class="btn btn-primary w-100">ログイン</button>
    </form>

    <div class="mt-3 text-center">
        <a href="{{ route('register') }}">新規登録はこちら</a>
    </div>
</div>
@endsection
