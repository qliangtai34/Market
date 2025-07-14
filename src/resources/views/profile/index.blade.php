@extends('layouts.app')

@section('content')
<div class="container">
    <h1>マイページ</h1>

    {{-- プロフィール情報 --}}
    <div class="mb-5">
        <h2>プロフィール</h2>
        <div class="d-flex align-items-center">
            {{-- プロフィール画像 --}}
            @if ($profile->image_url)
                <img src="{{ $profile->image_url }}" alt="プロフィール画像" class="rounded-circle me-3" width="100" height="100">
            @endif
            {{-- ニックネームのみ表示 --}}
            <h4 class="mb-0">{{ $profile->nickname }}</h4>
        </div>
        <a href="{{ route('profile.edit') }}" class="btn btn-sm btn-outline-primary mt-3">プロフィール編集</a>
    </div>

    {{-- 表示切り替えタブ --}}
    <div class="mb-4">
        <a href="{{ route('mypage.index', ['page' => 'sell']) }}" class="btn btn-outline-secondary me-2 {{ $mode === 'sell' ? 'active' : '' }}">出品した商品</a>
        <a href="{{ route('mypage.index', ['page' => 'buy']) }}" class="btn btn-outline-secondary {{ $mode === 'buy' ? 'active' : '' }}">購入した商品</a>
    </div>

    {{-- 出品した商品 --}}
    @if ($mode === 'sell' || is_null($mode))
    <div class="mb-5">
        <h2>出品した商品</h2>
        <div class="row">
            @forelse ($itemsSelling as $item)
                <div class="col-md-3 mb-3 text-center">
                    <img src="{{ asset($item->img_url) }}" alt="{{ $item->name }}" class="img-fluid" style="max-height: 150px;">
                    <p class="mt-2">{{ $item->name }}</p>
                </div>
            @empty
                <p>出品した商品はありません。</p>
            @endforelse
        </div>
    </div>
    @endif

    {{-- 購入した商品 --}}
    @if ($mode === 'buy' || is_null($mode))
    <div>
        <h2>購入した商品</h2>
        <div class="row">
            @forelse ($itemsPurchased as $item)
                <div class="col-md-3 mb-3 text-center">
                    <img src="{{ asset($item->img_url) }}" alt="{{ $item->name }}" class="img-fluid" style="max-height: 150px;">
                    <p class="mt-2">{{ $item->name }}</p>
                </div>
            @empty
                <p>購入した商品はありません。</p>
            @endforelse
        </div>
    </div>
    @endif
</div>
@endsection
