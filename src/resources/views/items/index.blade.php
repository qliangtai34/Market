@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
        <h1 class="mb-0">{{ $mode === 'mylist' ? 'マイリスト' : '商品一覧' }}</h1>

        @auth
            @php
                $queryParams = ['keyword' => $keyword];
            @endphp
            <div class="mt-2 mt-md-0">
                @if ($mode === 'mylist')
                    <a href="{{ route('items.index', $queryParams) }}" class="btn btn-outline-secondary me-2">商品一覧へ</a>
                @else
                    <a href="{{ route('items.mylist', $queryParams) }}" class="btn btn-outline-primary me-2">マイリスト</a>
                @endif

                <a href="{{ route('sell.create') }}" class="btn btn-success">商品出品</a>
            </div>
        @endauth
    </div>

    <form method="GET" action="{{ $mode === 'mylist' ? route('items.mylist') : route('items.index') }}" class="mb-4 d-flex flex-wrap gap-2">
        <input type="text" name="keyword" value="{{ $keyword }}" placeholder="商品名で検索" class="form-control w-100 w-md-50">
        <button type="submit" class="btn btn-primary">検索</button>
    </form>

    @php
        $likedItemIds = Auth::check() ? Auth::user()->likes->pluck('item_id')->toArray() : [];
    @endphp

    @if ($items->isEmpty())
        <p>商品が見つかりません。</p>
    @else
        <div class="row">
            @foreach ($items as $item)
                <div class="col-md-3 mb-4">
                    <div class="card h-100">
                        <a href="{{ route('items.show', ['item_id' => $item->id]) }}">
                            @if ($item->img_url)
                                <img src="{{ asset($item->img_url) }}" class="card-img-top" alt="{{ $item->name }}" style="height: 180px; object-fit: cover;">
                            @else
                                <img src="{{ asset('no-image.png') }}" class="card-img-top" alt="no image" style="height: 180px; object-fit: cover;">
                            @endif
                        </a>
                        <div class="card-body">
                            <h5 class="card-title">{{ $item->name }}</h5>

                            {{-- 商品ステータス --}}
                            @if ($item->buyers->isNotEmpty())
                                <span class="badge bg-danger">Sold</span>
                            @else
                                <span class="badge bg-success">販売中</span>
                            @endif

                            {{-- いいね済み表示 --}}
                            @if (in_array($item->id, $likedItemIds))
                                <span class="badge bg-primary ms-2">♥ いいね済</span>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
