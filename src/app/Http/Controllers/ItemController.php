<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Item;

class ItemController extends Controller
{
    /**
     * トップページ表示（商品一覧 or マイリスト）
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $keyword = $request->input('keyword', '');
        $page = $request->query('page'); // 'mylist' の場合に切り替え

        // 初期化
        $items = collect();
        $likedItemIds = [];

        // ▼ マイリスト（いいね一覧）
        if ($page === 'mylist') {
            if (!$user) {
                return redirect()->route('login');
            }

            $likedItems = $user->likes()->with('buyers')->latest()->get();

            // 検索キーワードで絞り込み
            if (!empty($keyword)) {
                $likedItems = $likedItems->filter(function ($item) use ($keyword) {
                    return str_contains($item->name, $keyword);
                });
            }

            $items = $likedItems;
            $likedItemIds = $items->pluck('id')->toArray();

            return view('items.index', [
                'items' => $items,
                'keyword' => $keyword,
                'mode' => 'mylist',
                'likedItemIds' => $likedItemIds,
            ]);
        }

        // ▼ 通常の商品一覧
        $query = Item::query()->with('buyers');

        // ログインユーザーの出品商品を除外
        if ($user) {
            $query->where('user_id', '!=', $user->id);
        }

        // 検索キーワード
        if (!empty($keyword)) {
            $query->where('name', 'like', '%' . $keyword . '%');
        }

        $items = $query->latest()->get();
        $likedItemIds = $user ? $user->likes()->pluck('item_id')->toArray() : [];

        return view('items.index', [
            'items' => $items,
            'keyword' => $keyword,
            'mode' => 'all',
            'likedItemIds' => $likedItemIds,
        ]);
    }

    /**
     * 商品詳細ページ表示
     */
    public function show($item_id)
    {
        $item = Item::with([
            'categories',
            'likedUsers',
            'comments.user',
        ])->withCount(['likedUsers', 'comments'])->findOrFail($item_id);

        $liked = Auth::check() && $item->likedUsers->contains('id', Auth::id());

        return view('items.show', compact('item', 'liked'));
    }

    /**
     * いいね切り替え処理
     */
    public function toggleLike($item_id)
    {
        $item = Item::findOrFail($item_id);
        $user = Auth::user();

        $item->likedUsers()->toggle($user->id); // toggleで簡潔に

        return back();
    }

    /**
     * コメント投稿処理
     */
    public function postComment(Request $request, $item_id)
    {
        $request->validate([
            'body' => 'required|max:255',
        ]);

        $item = Item::findOrFail($item_id);

        $item->comments()->create([
            'user_id' => Auth::id(),
            'body' => $request->body,
        ]);

        return back();
    }
}
