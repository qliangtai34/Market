@extends('layouts.app')

@section('content')
<div class="container">
    <h2>ログイン</h2>
    <form method="POST" action="{{ route('login.attempt') }}">
        @csrf
        <div>
            <label>メールアドレス</label>
            <input type="email" name="email" value="{{ old('email') }}">
            @error('email') <p style="color:red">{{ $message }}</p> @enderror
        </div>
        <div>
            <label>パスワード</label>
            <input type="password" name="password">
            @error('password') <p style="color:red">{{ $message }}</p> @enderror
        </div>
        <button type="submit">ログイン</button>
    </form>
    <p><a href="{{ route('register') }}">新規登録はこちら</a></p>
</div>
@endsection
