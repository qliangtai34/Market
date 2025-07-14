@extends('layouts.app')

@section('content')
<div class="container">
    <h2 class="mb-4">購入確認画面</h2>

    {{-- 商品情報 --}}
    <div class="card mb-4">
        <div class="row g-0">
            <div class="col-md-4">
                <img src="{{ asset($item->img_url ?? 'no-image.png') }}" alt="{{ $item->name }}" class="img-fluid rounded-start">
            </div>
            <div class="col-md-8">
                <div class="card-body">
                    <h3 class="card-title">{{ $item->name }}</h3>
                    <p class="card-text"><strong>ブランド名:</strong> {{ $item->brand ?? '（未設定）' }}</p>
                    <p class="card-text"><strong>価格:</strong> ¥{{ number_format($item->price) }}</p>
                    <p class="card-text"><strong>住所:</strong> {{ $address ?? '住所が未登録です' }}</p>
                    <a href="{{ route('purchase.address.edit', ['item_id' => $item->id]) }}" class="btn btn-outline-secondary mt-2">
                        配送先を変更する
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- 支払い方法 --}}
    <form method="POST" action="{{ route('purchase.process', $item->id) }}">
        @csrf

        <div class="form-group mb-3">
            <label for="payment_method" class="form-label">支払い方法:</label>
            <select name="payment_method" id="payment_method" class="form-select" required>
                <option value="card">カード支払い</option>
                <option value="convenience">コンビニ支払い</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">購入する</button>
    </form>
</div>
@endsection
