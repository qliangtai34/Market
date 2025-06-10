@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">会員登録</h2>

    <form method="POST" action="{{ route('register.store') }}">
        @csrf

        {{-- ユーザー名 --}}
        <div class="mb-3">
            <label for="name" class="form-label">ユーザー名</label>
            <input type="text" id="name" name="name" class="form-control" value="{{ old('name') }}">
            @error('name')
                <div class="text-danger small">{{ $message }}</div>
            @enderror
        </div>

        {{-- メールアドレス --}}
        <div class="mb-3">
            <label for="email" class="form-label">メールアドレス</label>
            <input type="text" id="email" name="email" class="form-control" value="{{ old('email') }}" autocomplete="off">
            @error('email')
                <div class="text-danger small">{{ $message }}</div>
            @enderror
        </div>

        {{-- パスワード --}}
        <div class="mb-3">
            <label for="password" class="form-label">パスワード</label>
            <input type="password" id="password" name="password" class="form-control">
            @error('password')
                <div class="text-danger small">{{ $message }}</div>
            @enderror
        </div>

        {{-- 確認用パスワード --}}
        <div class="mb-4">
            <label for="password_confirmation" class="form-label">確認用パスワード</label>
            <input type="password" id="password_confirmation" name="password_confirmation" class="form-control">
            @error('password_confirmation')
                <div class="text-danger small">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary">登録</button>
    </form>

    <p class="mt-3">
        <a href="{{ route('login') }}">ログインはこちら</a>
    </p>
</div>
@endsection
