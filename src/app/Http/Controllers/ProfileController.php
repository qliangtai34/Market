<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Profile;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    // マイページ表示（プロフィール、出品・購入商品表示）
    public function index()
    {
        $user = Auth::user();
        $profile = Profile::firstOrNew(['user_id' => $user->id]);

        $itemsSelling = $user->items()->where('is_sold', false)->get(); // 出品中の商品
        $itemsPurchased = $user->purchases()->get(); // リレーション名をpurchasesに修正

        return view('profile.index', compact('profile', 'itemsSelling', 'itemsPurchased'));
    }

    // プロフィール編集画面
    public function edit()
    {
        $user = Auth::user();
        $profile = Profile::firstOrNew(['user_id' => $user->id]);

        return view('profile.edit', compact('profile'));
    }

    // プロフィール更新処理
    public function update(Request $request)
    {
        $request->validate([
            'nickname' => 'required|string|max:255',
            'zipcode' => 'nullable|string|max:10',
            'address' => 'nullable|string|max:255',
            'building' => 'nullable|string|max:255', // 建物名を追加
            'image' => 'nullable|image|max:2048',
        ]);

        $user = Auth::user();
        $profile = Profile::firstOrNew(['user_id' => $user->id]);

        // プロフィール画像アップロード処理
        if ($request->hasFile('image')) {
            if ($profile->image_path) {
                Storage::disk('public')->delete($profile->image_path);
            }
            $path = $request->file('image')->store('profiles', 'public');
            $profile->image_path = $path;
        }

        // 各項目保存
        $profile->nickname = $request->nickname;
        $profile->zipcode = $request->zipcode;
        $profile->address = $request->address;
        $profile->building = $request->building; // 建物名の保存
        $profile->user_id = $user->id;
        $profile->save();

        return redirect('/')->with('success', 'プロフィールを更新しました。');
    }
}
