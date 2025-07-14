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
            'brand' => 'required|string|max:255',  // 追加
            'price' => 'required|integer|min:1|max:300000',
            'description' => 'required|string|max:1000',
            'condition' => 'required|string|in:新品,未使用に近い,目立った傷や汚れなし,やや傷や汚れあり,傷や汚れあり,全体的に状態が悪い',
            'categories' => 'required|array|min:1',
            'categories.*' => 'required|exists:categories,id',
            'image' => 'required|image|max:2048',
        ], [
            'name.required' => '商品名は必須です。',
            'brand.required' => 'ブランド名は必須です。',  // 追加
            'price.required' => '価格は必須です。',
            'description.required' => '商品説明は必須です。',
            'condition.required' => '商品の状態は必須です。',
            'categories.required' => 'カテゴリーを1つ以上選択してください。',
            'categories.*.exists' => '選択されたカテゴリーが無効です。',
            'image.required' => '商品画像は必須です。',
            'image.image' => '画像ファイルを選択してください。',
            'image.max' => '画像サイズは2MB以内でアップロードしてください。',
        ]);

        // 画像アップロード処理
        $img_url = null;
        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('items', 'public');
            $img_url = 'storage/' . $path;
        }

        // 商品登録
        $item = new Item();
        $item->name = $request->name;
        $item->brand = $request->brand;  // 追加
        $item->price = $request->price;
        $item->description = $request->description;
        $item->condition = $request->condition;
        $item->user_id = Auth::id();
        $item->img_url = $img_url;
        $item->is_sold = false;
        $item->save();

        // カテゴリ登録（中間テーブル）
        $item->categories()->sync($request->categories);

        return redirect()->route('items.show', $item->id)
                         ->with('success', '商品を出品しました。');
    }
}
