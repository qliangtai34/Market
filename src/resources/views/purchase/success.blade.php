<!-- resources/views/purchase/success.blade.php -->

@extends('layouts.app') {{-- 共通レイアウト使用時 --}}
@section('content')
<div class="container">
    <h2>ご購入ありがとうございます！</h2>

    <div class="card mt-4">
        <div class="card-body">
            <h5 class="card-title">購入商品</h5>
            <p class="card-text"><strong>商品名：</strong> {{ $item->name }}</p>
            <p class="card-text"><strong>価格：</strong> ¥{{ number_format($item->price) }}</p>
            <p class="card-text"><strong>購入日時：</strong> {{ now()->format('Y年m月d日 H:i') }}</p>
        </div>
    </div>

    <div class="mt-4">
        <a href="{{ route('items.index') }}" class="btn btn-primary">商品一覧へ戻る</a>
    </div>
</div>
@endsection
