<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Item;
use App\Models\User;
use Stripe\Stripe;
use Stripe\Webhook;

class StripeWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $payload = $request->getContent();
        $sig_header = $request->header('Stripe-Signature');
        $webhookSecret = config('services.stripe.webhook_secret');

        try {
            // Stripe イベントオブジェクトを構築
            $event = Webhook::constructEvent(
                $payload, $sig_header, $webhookSecret
            );
        } catch (\UnexpectedValueException $e) {
            // 無効なペイロード
            Log::error('Invalid payload');
            return response('Invalid payload', 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            // 署名不一致
            Log::error('Invalid signature');
            return response('Invalid signature', 400);
        }

        // checkout.session.completed イベント処理
        if ($event->type === 'checkout.session.completed') {
            $session = $event->data->object;

            $itemId = $session->metadata->item_id ?? null;
            $userId = $session->metadata->user_id ?? null;

            if ($itemId && $userId) {
                $item = Item::find($itemId);
                $user = User::find($userId);

                if ($item && $user && !$item->is_sold) {
                    // 購入履歴に追加（既に登録されていない場合のみ）
                    if (!$item->buyers()->where('user_id', $user->id)->exists()) {
                        $item->buyers()->attach($user->id, [
                            'address' => $user->address,
                            'purchased_at' => now(),
                        ]);
                    }

                    // is_sold を true に更新
                    $item->is_sold = true;
                    $item->save();

                    Log::info("Item ID {$item->id} marked as sold by user ID {$user->id}");
                }
            }
        }

        return response('Webhook handled', 200);
    }
}
