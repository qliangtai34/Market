<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SellController;

/*
|-------------------------------------------------------------------------- 
| ▼ 未ログインでもアクセス可能
|-------------------------------------------------------------------------- 
*/

// 商品一覧（トップページ） + 検索
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

    // マイリスト（いいねした商品一覧） + 検索対応
    Route::get('/mylist', [ItemController::class, 'mylist'])->name('items.mylist');

    // 商品にいいね・いいね解除（トグル）
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

    // 商品購入確認画面
    Route::get('/purchase/{item_id}', [PurchaseController::class, 'show'])->name('purchase.show');

    // 商品購入処理（支払い実行）
    Route::post('/purchase/{item_id}', [PurchaseController::class, 'process'])->name('purchase.process');

    // 配送先住所変更画面・更新処理
    Route::get('/purchase/address/{item_id}', [PurchaseController::class, 'editAddress'])->name('purchase.address.edit');
    Route::post('/purchase/address/{item_id}', [PurchaseController::class, 'updateAddress'])->name('purchase.address.update');

    // 商品出品画面・登録処理
    Route::get('/sell', [SellController::class, 'create'])->name('sell.create');
    Route::post('/sell', [SellController::class, 'store'])->name('sell.store');

    // プロフィール編集画面・更新処理
    Route::get('/mypage/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/mypage/profile', [ProfileController::class, 'update'])->name('profile.update');

    // マイページ（購入・出品商品一覧などのトップ）
    Route::get('/mypage', [ProfileController::class, 'index'])->name('mypage.index');

    // 任意：ダッシュボード画面
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});
