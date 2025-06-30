@extends('layouts.app')

@section('content')
<div class="container">
    <h2>購入確認画面</h2>

    <div>
    <img src="{{ asset($item->img_url) }}" alt="{{ $item->name }}" width="200">

        <h3>{{ $item->name }}</h3>
        <p>価格: ¥{{ number_format($item->price) }}</p>
        <p>送付先住所: {{ $address }}</p>
    </div>
    
    <a href="{{ route('purchase.address.edit', ['item_id' => $item->id]) }}" class="btn btn-outline-secondary mt-3">
    配送先を変更する
    </a>

    <form method="POST" action="{{ route('purchase.process', $item->id) }}">
        @csrf

        <div class="form-group mt-3">
            <label for="payment_method">支払い方法:</label>
            <select name="payment_method" class="form-control" required>
                <option value="card">カード支払い</option>
                <option value="convenience">コンビニ支払い</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary mt-3">購入する</button>
    </form>
</div>
@endsection
