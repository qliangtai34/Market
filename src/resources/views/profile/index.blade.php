@extends('layouts.app')

@section('content')
<div class="container">
    <h1>マイページ</h1>

    <div class="card mt-4">
        <div class="card-body">
            <p><strong>ニックネーム:</strong> {{ $profile->nickname ?? '未設定' }}</p>
            <p><strong>住所:</strong> {{ $profile->address ?? '未設定' }}</p>

            {{-- 正しいルート名に修正 --}}
            <a href="{{ route('profile.edit') }}" class="btn btn-primary">プロフィールを編集</a>
        </div>
    </div>
</div>
@endsection
