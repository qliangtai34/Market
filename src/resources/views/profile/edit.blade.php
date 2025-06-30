@extends('layouts.app')

@section('content')
<div class="container">
    <h1>プロフィール編集</h1>

    {{-- エラーメッセージ表示 --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT') {{-- PUTメソッド指定 --}}

        {{-- ユーザー名 --}}
        <div class="mb-3">
            <label for="nickname" class="form-label">ユーザー名</label>
            <input type="text" name="nickname" id="nickname" class="form-control"
                   value="{{ old('nickname', $profile->nickname) }}" required>
        </div>

        {{-- 郵便番号 --}}
        <div class="mb-3">
            <label for="zipcode" class="form-label">郵便番号</label>
            <input type="text" name="zipcode" id="zipcode" class="form-control"
                   value="{{ old('zipcode', $profile->zipcode) }}">
        </div>

        {{-- 住所 --}}
        <div class="mb-3">
            <label for="address" class="form-label">住所</label>
            <input type="text" name="address" id="address" class="form-control"
                   value="{{ old('address', $profile->address) }}">
        </div>

        {{-- 建物名（新規追加） --}}
        <div class="mb-3">
            <label for="building" class="form-label">建物名</label>
            <input type="text" name="building" id="building" class="form-control"
                   value="{{ old('building', $profile->building) }}">
        </div>

        {{-- プロフィール画像 --}}
        <div class="mb-3">
            <label for="image" class="form-label">プロフィール画像</label>
            <input type="file" name="image" id="image" class="form-control" accept="image/*">

            @if ($profile->image_path)
                <div class="mt-2">
                    <img src="{{ asset('storage/' . $profile->image_path) }}" alt="現在の画像" width="100">
                </div>
            @endif
        </div>

        <button type="submit" class="btn btn-primary">保存</button>
    </form>
</div>
@endsection
