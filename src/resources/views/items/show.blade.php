@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        {{-- 商品画像 --}}
        <div class="col-md-6">
            <img src="{{ asset($item->img_url ?? 'no-image.png') }}" alt="{{ $item->name }}" class="img-fluid">
        </div>

        {{-- 商品情報 --}}
        <div class="col-md-6">
            <h2>{{ $item->name }}</h2>
            <p><strong>ブランド:</strong> {{ $item->brand }}</p>
            <p><strong>価格:</strong> ¥{{ number_format($item->price) }}</p>

            {{-- いいねとコメント数 --}}
            <p>
                @auth
                    <form action="{{ route('items.like', ['item_id' => $item->id]) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-link p-0" title="いいねする／取り消す" aria-label="いいね">
                            <i class="bi bi-heart{{ $liked ? '-fill text-danger' : '' }}"></i>
                            <span class="ms-1">{{ $item->liked_users_count }}</span>
                        </button>
                    </form>
                @else
                    <i class="bi bi-heart"></i>
                    <span class="ms-1">{{ $item->liked_users_count }}</span>
                    <small class="text-muted">(いいねするにはログインが必要です)</small>
                @endauth

                <i class="bi bi-chat-left-text ms-3" title="コメント数"></i>
                <span class="ms-1">{{ $item->comments_count }}</span>
            </p>

            {{-- 商品説明 --}}
            <p>{{ $item->description }}</p>

            {{-- カテゴリ・状態 --}}
            <p><strong>カテゴリ:</strong>
                @foreach ($item->categories as $category)
                    <span class="badge bg-secondary">{{ $category->name }}</span>
                @endforeach
            </p>
            <p><strong>状態:</strong> {{ $item->condition }}</p>

            {{-- 購入ボタン --}}
            @auth
                @if (!$item->is_sold)
                    <a href="{{ route('purchase.show', ['item_id' => $item->id]) }}" class="btn btn-primary">購入手続きへ</a>
                @else
                    <span class="badge bg-danger">SOLD</span>
                @endif
            @else
                <p><a href="{{ route('login') }}" class="btn btn-outline-secondary">ログインして購入</a></p>
            @endauth
        </div>
    </div>

    <hr>

    {{-- コメント表示 --}}
    <div class="mt-4">
        <h4>コメント（{{ $item->comments_count }}件）</h4>
        @forelse ($item->comments as $comment)
            <div class="mb-3 border-bottom pb-2">
                <strong>{{ $comment->user->name }}</strong>
                <p class="mb-1">{{ $comment->body }}</p>
            </div>
        @empty
            <p>コメントはまだありません。</p>
        @endforelse
    </div>

    {{-- コメント投稿フォーム --}}
    @auth
        <div class="mt-4">
            <form action="{{ route('items.comment', ['item_id' => $item->id]) }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="body" class="form-label">コメント</label>
                    <textarea name="body" class="form-control" rows="3" required maxlength="255"></textarea>
                    @error('body')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>
                <button type="submit" class="btn btn-secondary">送信</button>
            </form>
        </div>
    @else
        <p class="mt-4">コメントを投稿するには <a href="{{ route('login') }}">ログイン</a> が必要です。</p>
    @endauth
</div>
@endsection
