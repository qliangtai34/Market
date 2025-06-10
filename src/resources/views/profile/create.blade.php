<!-- resources/views/profile/create.blade.php -->
@extends('layouts.app')

@section('content')
<div class="container">
    <h1>プロフィール登録ページ</h1>
    <form method="POST" action="{{ route('profile.store') }}">
        @csrf
        <div>
            <label>ユーザー名</label>
            <input type="text" name="name" value="{{ old('name') }}">
            @error('name') <p style="color:red">{{ $message }}</p> @enderror
        </div>
        <!-- 他のプロフィール入力項目を追加 -->
        <button type="submit">登録</button>
    </form>
</div>
@endsection
