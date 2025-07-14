<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Profile;

class ProfileController extends Controller
{
    /**
     * マイページ表示（プロフィール、出品・購入商品一覧）
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $profile = Profile::firstOrNew(['user_id' => $user->id]);

        // クエリパラメータから mode（buy / sell）を取得
        $mode = $request->query('page'); // null / 'buy' / 'sell'

        // 出品商品（未売却）
        $itemsSelling = $user->items()->where('is_sold', false)->get();

        // 購入商品（多対多リレーションのまま取得）
        $itemsPurchased = $user->purchases; // ここは with('item') ではなく直接取得

        return view('profile.index', compact(
            'profile',
            'itemsSelling',
            'itemsPurchased',
            'mode'
        ));
    }

    /**
     * プロフィール編集画面
     */
    public function edit()
    {
        $user = Auth::user();
        $profile = Profile::firstOrNew(['user_id' => $user->id]);

        return view('profile.edit', compact('profile'));
    }

    /**
     * プロフィール更新処理
     */
    public function update(Request $request)
    {
        $request->validate([
            'nickname' => 'required|string|max:255',
            'zipcode'  => 'nullable|string|max:10',
            'address'  => 'nullable|string|max:255',
            'building' => 'nullable|string|max:255',
            'image'    => 'nullable|image|max:2048',
        ]);

        $user = Auth::user();
        $profile = Profile::firstOrNew(['user_id' => $user->id]);

        // プロフィール画像の保存
        if ($request->hasFile('image')) {
            // 既存の画像がある場合は削除
            if ($profile->image_path) {
                Storage::disk('public')->delete($profile->image_path);
            }
            $path = $request->file('image')->store('profiles', 'public');
            $profile->image_path = $path;
        }

        // フォームの各項目を保存
        $profile->nickname = $request->nickname;
        $profile->zipcode  = $request->zipcode;
        $profile->address  = $request->address;
        $profile->building = $request->building;
        $profile->user_id  = $user->id;
        $profile->save();

        return redirect()->route('mypage.index')->with('success', 'プロフィールを更新しました。');
    }
}
