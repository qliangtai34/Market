<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Item;
use App\Models\User;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use Illuminate\Support\Facades\URL;

class PurchaseController extends Controller
{
    /**
     * 購入確認画面（FN021）
     */
    public function show($item_id)
    {
        $item = Item::findOrFail($item_id);
        $user = Auth::user();
        $address = $user->address ?? '未登録';

        return view('purchase.show', compact('item', 'address'));
    }

    /**
     * 購入処理実行（Stripe Checkout）FN022・FN023
     */
    public function process(Request $request, $item_id)
    {
        $request->validate([
            'payment_method' => 'required|in:card,convenience',
        ]);

        $item = Item::findOrFail($item_id);
        $user = Auth::user();

        // すでに購入されていないかチェック
        if ($item->buyers()->where('user_id', $user->id)->exists()) {
            return redirect()->route('items.index')->with('error', 'この商品はすでに購入済みです。');
        }

        // Stripe APIキー設定
        Stripe::setApiKey(config('services.stripe.secret'));

        // 支払い方法に応じた設定
        $payment_methods = $request->payment_method === 'convenience'
            ? ['konbini']  // Stripeの「コンビニ」コード
            : ['card'];    // カード決済

        // Stripe Checkout セッション作成
        $session = Session::create([
            'payment_method_types' => $payment_methods,
            'line_items' => [[
                'price_data' => [
                    'currency' => 'jpy',
                    'product_data' => [
                        'name' => $item->name,
                    ],
                    'unit_amount' => $item->price,
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => URL::to('/purchase/success?item_id=' . $item->id),
            'cancel_url' => URL::to('/purchase/cancel'),
            'metadata' => [
                'item_id' => $item->id,
                'user_id' => $user->id,
            ],
        ]);

        return redirect($session->url);
    }

    /**
     * Stripe決済成功後の画面
     */
    public function success(Request $request)
    {
        $item_id = $request->query('item_id');
        $user = Auth::user();
        $item = Item::findOrFail($item_id);

        // すでに購入済みでない場合のみ保存
        if (!$item->buyers()->where('user_id', $user->id)->exists()) {
            $item->buyers()->attach($user->id, [
                'address' => $user->address,
                'purchased_at' => now(),
            ]);
        }

        return view('purchase.success', compact('item'));
    }

    /**
     * Stripeキャンセル画面
     */
    public function cancel()
    {
        return view('purchase.cancel');
    }

    /**
     * 住所変更画面（FN024）
     */
    public function editAddress($item_id)
    {
        $item = Item::findOrFail($item_id);
        $user = Auth::user();

        return view('purchase.address', compact('item', 'user'));
    }

    /**
     * 住所更新処理（FN024）
     */
    public function updateAddress(Request $request, $item_id)
    {
        $request->validate([
            'address' => 'required|string|max:255',
        ]);

        $user = Auth::user();
        $user->address = $request->address;
        $user->save();

        return redirect()->route('purchase.show', ['item_id' => $item_id])
                         ->with('success', '住所が更新されました。');
    }
}
