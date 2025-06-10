<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Item;

class ItemController extends Controller
{
    /**
     * トップページ表示（商品一覧）
     */
    public function index(Request $request)
    {
        $keyword = $request->input('keyword', '');

        $query = Item::query();

        // ログインユーザーの出品商品を除外
        if (Auth::check()) {
            $query->where('user_id', '!=', Auth::id());
        }

        // 商品名による部分一致検索
        if (!empty($keyword)) {
            $query->where('name', 'like', '%' . $keyword . '%');
        }

        $items = $query->with('buyers')->latest()->get();

        return view('items.index', [
            'items' => $items,
            'keyword' => $keyword,
            'mode' => 'all',
        ]);
    }

    /**
     * マイリスト表示（いいねした商品）
     */
    public function mylist(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        $keyword = $request->input('keyword', '');

        $likedItemIds = $user->likes()->pluck('item_id');

        $query = Item::whereIn('id', $likedItemIds);

        if (!empty($keyword)) {
            $query->where('name', 'like', '%' . $keyword . '%');
        }

        $items = $query->with('buyers')->latest()->get();

        return view('items.index', [
            'items' => $items,
            'keyword' => $keyword,
            'mode' => 'mylist',
        ]);
    }

    /**
     * 商品詳細ページ表示
     */
    public function show($item_id)
    {
        $item = Item::with([
            'categories',
            'likes',
            'comments.user',
        ])
        ->withCount(['likes', 'comments'])
        ->findOrFail($item_id);

        $liked = false;
        if (Auth::check()) {
            $liked = $item->likes->contains('user_id', Auth::id());
        }

        return view('items.show', compact('item', 'liked'));
    }

    /**
     * いいね切り替え処理
     */
    public function toggleLike($item_id)
    {
        $item = Item::findOrFail($item_id);
        $user = Auth::user();

        if ($item->likes()->where('user_id', $user->id)->exists()) {
            $item->likes()->detach($user->id);
        } else {
            $item->likes()->attach($user->id);
        }

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
