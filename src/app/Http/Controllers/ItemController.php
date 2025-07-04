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

        // ログインユーザーのいいね商品 ID を取得（表示用バッジに使用）
        $likedItemIds = Auth::check()
            ? Auth::user()->likes()->pluck('item_id')->toArray()
            : [];

        return view('items.index', [
            'items' => $items,
            'keyword' => $keyword,
            'mode' => 'all',
            'likedItemIds' => $likedItemIds,
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

        $user = Auth::user()->load('likes'); // いいね済商品を事前に読み込む
        $keyword = $request->input('keyword', '');

        $likedItemIds = $user->likes->pluck('id')->toArray();

        // 該当する item_id がない場合、空コレクションを返す
        if (empty($likedItemIds)) {
            return view('items.index', [
                'items' => collect(),
                'keyword' => $keyword,
                'mode' => 'mylist',
                'likedItemIds' => [],
            ]);
        }

        $query = Item::whereIn('id', $likedItemIds);

        if (!empty($keyword)) {
            $query->where('name', 'like', '%' . $keyword . '%');
        }

        $items = $query->with('buyers')->latest()->get();

        return view('items.index', [
            'items' => $items,
            'keyword' => $keyword,
            'mode' => 'mylist',
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
            'likedUsers',    // 変更
            'comments.user',
        ])
        ->withCount(['likedUsers', 'comments'])  // 変更
        ->findOrFail($item_id);

        $liked = false;
        if (Auth::check()) {
            $liked = $item->likedUsers->contains('id', Auth::id());  // 変更
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

        if ($item->likedUsers()->where('user_id', $user->id)->exists()) {  // 変更
            $item->likedUsers()->detach($user->id);  // 変更
        } else {
            $item->likedUsers()->attach($user->id);  // 変更
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
