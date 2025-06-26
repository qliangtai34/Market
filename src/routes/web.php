<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SellController;
use App\Http\Controllers\StripeWebhookController;

/*
|--------------------------------------------------------------------------
| ▼ 未ログインでもアクセス可能
|--------------------------------------------------------------------------
*/

// 商品一覧（トップページ）+ 検索
Route::get('/', [ItemController::class, 'index'])->name('items.index');

// 商品詳細ページ
Route::get('/item/{item_id}', [ItemController::class, 'show'])->name('items.show');

// 会員登録
Route::get('/register', [RegisterController::class, 'create'])->name('register');
Route::post('/register', [RegisterController::class, 'store'])->name('register.store');

// ログイン・ログアウト
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.attempt');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| ▼ ログイン済み（メール認証前でもアクセス可能）
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    // マイリスト（いいねした商品一覧）
    Route::get('/mylist', [ItemController::class, 'mylist'])->name('items.mylist');

    // 商品へのいいね（トグル）
    Route::post('/item/{item_id}/like', [ItemController::class, 'toggleLike'])->name('items.like');

    // 商品へのコメント投稿
    Route::post('/item/{item_id}/comment', [ItemController::class, 'postComment'])->name('items.comment');

    // メール認証案内ページ
    Route::get('/email/verify', function () {
        return view('auth.verify-email');
    })->name('verification.notice');
});

/*
|--------------------------------------------------------------------------
| ▼ ログイン済み ＆ メール認証済み
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {

    // 商品購入画面
    Route::get('/purchase/{item_id}', [PurchaseController::class, 'show'])->name('purchase.show');

    // 商品購入処理（Stripe支払い）
    Route::post('/purchase/{item_id}', [PurchaseController::class, 'process'])->name('purchase.process');

    // 配送先住所編集・更新
    Route::get('/purchase/address/{item_id}', [PurchaseController::class, 'editAddress'])->name('purchase.address.edit');
    Route::post('/purchase/address/{item_id}', [PurchaseController::class, 'updateAddress'])->name('purchase.address.update');

    // 商品出品
    Route::get('/sell', [SellController::class, 'create'])->name('sell.create');
    Route::post('/sell', [SellController::class, 'store'])->name('sell.store');

    // プロフィール編集
    Route::get('/mypage/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/mypage/profile', [ProfileController::class, 'update'])->name('profile.update');

    // マイページ（購入・出品商品一覧）
    Route::get('/mypage', [ProfileController::class, 'index'])->name('mypage.index');

    // 任意：ユーザーダッシュボード
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});

/*
|--------------------------------------------------------------------------
| ▼ Stripe Webhook（支払い完了通知）
|--------------------------------------------------------------------------
*/
Route::post('/stripe/webhook', [StripeWebhookController::class, 'handle']);

/*
|--------------------------------------------------------------------------
| ▼ Stripe決済完了後の画面（未ログイン状態でも遷移可能）
|--------------------------------------------------------------------------
*/
Route::get('/purchase/success', [PurchaseController::class, 'success'])->name('purchase.success');
Route::get('/purchase/cancel', [PurchaseController::class, 'cancel'])->name('purchase.cancel');
