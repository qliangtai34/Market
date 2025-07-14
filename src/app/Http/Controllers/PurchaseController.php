<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use App\Models\Item;
use App\Models\User;
use App\Models\Profile;
use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession;

class PurchaseController extends Controller
{
    // 購入確認画面
    public function show($item_id)
    {
        $item = Item::findOrFail($item_id);
        $user = Auth::user();

        if ($item->is_sold) {
            return redirect()->route('items.index')->with('error', 'この商品はすでに売り切れました。');
        }

        $buyer = $item->buyers()->where('user_id', $user->id)->first();
        $pivot = $buyer ? $buyer->pivot : null;

        $profile = $user->profile;
        $address = ($pivot && $pivot->address) ? $pivot->address : (($profile && $profile->address) ? $profile->address : '未登録');

        return view('purchase.show', compact('item', 'address'));
    }

    // Stripe決済処理
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
        $paymentMethods = $request->payment_method === 'convenience' ? ['konbini'] : ['card'];

        try {
            $session = StripeSession::create([
                'payment_method_types' => $paymentMethods,
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'jpy',
                        'product_data' => ['name' => $item->name],
                        'unit_amount' => $item->price,
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

    // 購入成功後の処理
    public function success(Request $request)
    {
        $session_id = $request->query('session_id');
        if (!$session_id) {
            return redirect()->route('items.index')->with('error', '不正なアクセスです。');
        }

        Stripe::setApiKey(config('services.stripe.secret'));

        try {
            $session = StripeSession::retrieve($session_id);
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

            $buyer = $item->buyers()->where('user_id', $user_id)->first();
            $pivot = $buyer ? $buyer->pivot : null;

            $profile = $user->profile;
            $address = ($pivot && $pivot->address) ? $pivot->address : (($profile && $profile->address) ? $profile->address : '未登録');

            Log::info("✅ 購入処理開始: item_id={$item_id}, user_id={$user_id}");

            if (!$item->is_sold) {
                if (!$pivot) {
                    $item->buyers()->attach($user_id, [
                        'address' => $address,
                        'purchased_at' => now(),
                    ]);
                } else {
                    $item->buyers()->updateExistingPivot($user_id, [
                        'address' => $address,
                        'purchased_at' => now(),
                    ]);
                }

                $item->is_sold = true;
                $item->save();
            }

            return view('purchase.success', compact('item'));

        } catch (\Exception $e) {
            Log::error('購入処理エラー: ' . $e->getMessage());
            return redirect()->route('items.index')->with('error', '購入処理中にエラーが発生しました。');
        }
    }

    // キャンセル表示
    public function cancel()
    {
        return view('purchase.cancel');
    }

    // 配送先編集画面表示
    public function editAddress($item_id)
    {
        $item = Item::findOrFail($item_id);
        $user = Auth::user();

        $buyer = $item->buyers()->where('user_id', $user->id)->first();
        $pivot = $buyer ? $buyer->pivot : null;

        $profile = $user->profile;
        $address = ($pivot && $pivot->address) ? $pivot->address : (($profile && $profile->address) ? $profile->address : '');

        return view('purchase.edit_address', compact('item', 'address'));
    }

    // 配送先更新処理（purchase_items テーブルに保存）
    public function updateAddress(Request $request, $item_id)
    {
        $request->validate([
            'address' => 'required|string|max:255',
        ]);

        $item = Item::findOrFail($item_id);
        $user = Auth::user();

        if ($item->buyers()->where('user_id', $user->id)->exists()) {
            $item->buyers()->updateExistingPivot($user->id, [
                'address' => $request->address,
            ]);
        } else {
            $item->buyers()->attach($user->id, [
                'address' => $request->address,
                'purchased_at' => null,
            ]);
        }

        return redirect()->route('purchase.show', $item->id)
                         ->with('success', '配送先住所が更新されました。');
    }
}
