<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Item;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class SellController extends Controller
{
    // 出品画面表示
    public function create()
    {
        $categories = Category::all();

        // 商品の状態（参考UIに合わせて適宜変更してください）
        $conditions = [
            '新品',
            '未使用に近い',
            '目立った傷や汚れなし',
            'やや傷や汚れあり',
            '傷や汚れあり',
            '全体的に状態が悪い',
        ];

        return view('sell.create', compact('categories', 'conditions'));
    }

    // 出品処理保存
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|integer|min:1|max:300000',
            'description' => 'required|string',
            'condition' => 'required|string',
            'categories' => 'required|array',
            'categories.*' => 'exists:categories,id',
            'image' => 'nullable|image|max:2048',
        ]);

        // 画像アップロード処理
        $img_url = null;
        if ($request->hasFile('image')) {
            // public/items フォルダに保存（storage/app/public/items）
            $path = $request->file('image')->store('items', 'public');
            // 画像URL取得（例: /storage/items/xxxx.jpg）
            $img_url = 'storage/' . $path;
        }

        // Itemモデルに保存
        $item = new Item();
        $item->name = $request->name;
        $item->price = $request->price;
        $item->description = $request->description;
        $item->condition = $request->condition;
        $item->user_id = Auth::id();
        $item->img_url = $img_url;
        $item->is_sold = false;
        $item->save();

        // カテゴリを紐付け（多対多）
        $item->categories()->sync($request->categories);

        return redirect()->route('items.show', $item->id)->with('success', '商品を出品しました。');
    }
}
