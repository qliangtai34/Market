{{-- resources/views/profile/edit.blade.php --}}
@extends('layouts.app') {{-- 共通レイアウトがある場合 --}}
@section('content')
    <h2>プロフィール編集</h2>

    <form method="POST" action="{{ route('profile.update') }}">
        @csrf
        @method('PUT')

        <div>
            <label>ニックネーム</label><br>
            <input type="text" name="nickname" value="{{ old('nickname', $profile->nickname) }}" required>
        </div>

        <div>
            <label>住所</label><br>
            <input type="text" name="address" value="{{ old('address', $profile->address) }}">
        </div>

        <button type="submit">保存</button>
    </form>
@endsection
