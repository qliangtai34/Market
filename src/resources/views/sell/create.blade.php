@extends('layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">商品出品</h1>

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

    <form action="{{ route('sell.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="mb-3">
            <label for="name" class="form-label">商品名</label>
            <input type="text" name="name" class="form-control" id="name" value="{{ old('name') }}" required>
        </div>

        <div class="mb-3">
            <label for="price" class="form-label">価格（円）</label>
            <input type="number" name="price" class="form-control" id="price" value="{{ old('price') }}" min="1" max="300000" required>
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">商品説明</label>
            <textarea name="description" class="form-control" id="description" rows="4" required>{{ old('description') }}</textarea>
        </div>

        <div class="mb-3">
            <label for="condition" class="form-label">商品の状態</label>
            <select name="condition" id="condition" class="form-select" required>
                <option value="">選択してください</option>
                @foreach ($conditions as $condition)
                    <option value="{{ $condition }}" @if(old('condition') === $condition) selected @endif>{{ $condition }}</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">カテゴリー（複数選択可）</label>
            @foreach ($categories as $category)
                <div class="form-check">
                    <input 
                        class="form-check-input" 
                        type="checkbox" 
                        name="categories[]" 
                        value="{{ $category->id }}" 
                        id="category_{{ $category->id }}"
                        {{ in_array($category->id, old('categories', [])) ? 'checked' : '' }}>
                    <label class="form-check-label" for="category_{{ $category->id }}">
                        {{ $category->name }}
                    </label>
                </div>
            @endforeach
        </div>

        <div class="mb-3">
            <label for="image" class="form-label">商品画像（任意）</label>
            <input class="form-control" type="file" name="image" id="image" accept="image/*">
            <small class="form-text text-muted">※画像は2MBまでアップロード可能</small>
        </div>

        <button type="submit" class="btn btn-primary">出品する</button>
    </form>
</div>
@endsection
