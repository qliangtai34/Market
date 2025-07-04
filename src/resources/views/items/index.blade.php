@extends('layouts.app')

@section('content')
<div class="container-xl">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <h1 class="mb-0">{{ $mode === 'mylist' ? 'マイリスト' : '商品一覧' }}</h1>

        @auth
            @php
                $queryParams = ['keyword' => $keyword];
            @endphp
            <div class="mt-2 mt-md-0 d-flex gap-2">
                @if ($mode === 'mylist')
                    <a href="{{ route('items.index', $queryParams) }}" class="btn btn-outline-secondary">商品一覧へ</a>
                @else
                    <a href="{{ route('items.mylist', $queryParams) }}" class="btn btn-outline-primary">マイリスト</a>
                @endif

                <a href="{{ route('sell.create') }}" class="btn btn-success">商品出品</a>
            </div>
        @endauth
    </div>

    @auth
        <div class="mb-3">
            <a href="{{ route('mypage.index') }}" class="btn btn-outline-primary">マイページへ</a>
        </div>
    @endauth

    {{-- 検索フォーム --}}
    <form method="GET" action="{{ $mode === 'mylist' ? route('items.mylist') : route('items.index') }}" class="mb-4 row g-2">
        <div class="col-12 col-md-8">
            <input
                type="text"
                name="keyword"
                value="{{ old('keyword') ?? $keyword }}"
                placeholder="商品名で検索"
                class="form-control"
                autocomplete="off"
            >
        </div>
        <div class="col-12 col-md-4">
            <button type="submit" class="btn btn-primary w-100">検索</button>
        </div>
    </form>

    @php
        $likedItemIds = $likedItemIds ?? [];
    @endphp

    @if ($items->isEmpty())
        <p>商品が見つかりません。</p>
    @else
        <div class="row">
            @foreach ($items as $item)
                <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-4">
                    <div class="card h-100 shadow-sm d-flex flex-column">
                        @if ($item->is_sold)
                            {{-- 売り切れ：非リンク + グレースタイル --}}
                            <div style="opacity: 0.6; pointer-events: none;">
                                <img src="{{ asset($item->img_url ?: 'no-image.png') }}"
                                     class="card-img-top"
                                     alt="{{ $item->name }}"
                                     style="height: 180px; object-fit: cover;">
                            </div>
                        @else
                            <a href="{{ route('items.show', ['item_id' => $item->id]) }}">
                                <img src="{{ asset($item->img_url ?: 'no-image.png') }}"
                                     class="card-img-top"
                                     alt="{{ $item->name }}"
                                     style="height: 180px; object-fit: cover;">
                            </a>
                        @endif

                        <div class="card-body d-flex flex-column justify-content-between">
                            <div>
                                <h5 class="card-title text-truncate">{{ $item->name }}</h5>
                                <p class="card-text mb-2">¥{{ number_format($item->price) }}</p>
                            </div>

                            <div class="mt-auto">
                                @if ($item->is_sold)
                                    <span class="badge bg-danger">Sold</span>
                                @else
                                    <span class="badge bg-success">販売中</span>
                                @endif

                                @if (in_array($item->id, $likedItemIds))
                                    <span class="badge bg-primary ms-2">♥ いいね済</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
