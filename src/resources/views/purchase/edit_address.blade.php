@extends('layouts.app')

@section('content')
<div class="container">
    <h2>配送先住所の変更</h2>

    {{-- エラーメッセージ --}}
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- 成功メッセージ --}}
    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <form action="{{ route('purchase.address.update', ['item_id' => $item->id]) }}" method="POST">
        @csrf

        <div class="mb-3">
            <label for="address" class="form-label">住所</label>
            <input type="text" name="address" class="form-control" id="address" value="{{ old('address', $address) }}" required>
        </div>

        <button type="submit" class="btn btn-primary">更新する</button>
        <a href="{{ route('purchase.show', $item->id) }}" class="btn btn-secondary ms-2">戻る</a>
    </form>
</div>
@endsection
