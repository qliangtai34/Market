<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use App\Models\Item;
use App\Models\User;
use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;

class PurchaseController extends Controller
{
    public function show($item_id)
    {
        $item = Item::findOrFail($item_id);

        if ($item->is_sold) {
            return redirect()->route('items.index')->with('error', 'この商品はすでに売り切れました。');
        }

        $user = Auth::user();
        $address = $user->address ?? '未登録';

        return view('purchase.show', compact('item', 'address'));
    }

    public function process(Request $request, $item_id)
    {
        \Log::info("✅ process() 呼び出し: item_id={$item_id}");

        $request->validate([
            'payment_method' => 'required|in:card,convenience',
        ]);

        $item = Item::findOrFail($item_id);
        $user = Auth::user();

        if ($item->is_sold) {
            return redirect()->route('items.index')->with('error', 'この商品はすでに購入済みです。');
        }

        Stripe::setApiKey(config('services.stripe.secret'));
        $payment_methods = $request->payment_method === 'convenience' ? ['konbini'] : ['card'];

        try {
            $session = StripeSession::create([
                'payment_method_types' => $payment_methods,
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'jpy',
                        'product_data' => [
                            'name' => $item->name,
                        ],
                        'unit_amount' => $item->price, // ¥であれば1円単位でOK（JPYは少数なし）
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'success_url' => URL::to('/purchase/success?session_id={CHECKOUT_SESSION_ID}'),
                'cancel_url' => URL::to('/purchase/cancel'),
                'metadata' => [
                    'item_id' => $item->id,
                    'user_id' => $user->id,
                ],
            ]);

            return redirect($session->url);
        } catch (\Exception $e) {
            Log::error('Stripe セッション作成エラー: ' . $e->getMessage());
            return back()->with('error', '決済処理に失敗しました。');
        }
    }

    public function success(Request $request)
    {
        $session_id = $request->query('session_id');
        if (!$session_id) {
            return redirect()->route('items.index')->with('error', '不正なアクセスです。');
        }

        Stripe::setApiKey(config('services.stripe.secret'));

        try {
            $session = \Stripe\Checkout\Session::retrieve($session_id);
        } catch (\Exception $e) {
            Log::error('セッション取得失敗: ' . $e->getMessage());
            return redirect()->route('items.index')->with('error', 'セッション取得に失敗しました。');
        }

        if ($session->payment_status !== 'paid') {
            return redirect()->route('items.index')->with('error', '決済が完了していません。');
        }

        $item_id = $session->metadata->item_id;
        $user_id = $session->metadata->user_id;

        try {
            $item = Item::findOrFail($item_id);
            $user = User::findOrFail($user_id);

            Log::info("✅ 購入処理開始: item_id={$item_id}, user_id={$user_id}");

            if ($item->is_sold) {
                Log::info("⚠️ 商品はすでに売却済み");
                return view('purchase.success', compact('item'));
            }

            if (!$item->buyers()->where('user_id', $user_id)->exists()) {
                $item->buyers()->attach($user_id, [
                    'address' => $user->address,
                    'purchased_at' => now(),
                ]);
                Log::info("✅ attach 成功: user_id={$user_id}");

                $item->is_sold = true;
                $saveResult = $item->save();
                Log::info("✅ is_sold 保存結果: " . ($saveResult ? '成功' : '失敗'));
            } else {
                Log::info("⚠️ すでにこのユーザーは購入済み");
            }
        } catch (\Exception $e) {
            Log::error('購入処理エラー: ' . $e->getMessage());
            Log::error($e->getTraceAsString());
            return redirect()->route('items.index')->with('error', '購入処理中にエラーが発生しました。');
        }

        return view('purchase.success', compact('item'));
    }

    public function cancel()
    {
        return view('purchase.cancel');
    }

    public function editAddress($item_id)
    {
        $item = Item::findOrFail($item_id);
        $user = Auth::user();

        return view('purchase.address', compact('item', 'user'));
    }

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
