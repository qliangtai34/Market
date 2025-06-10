<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Profile;

class ProfileController extends Controller
{
    // マイページ表示（プロフィール簡易表示などに使用）
    public function index()
    {
        $user = Auth::user();
        $profile = Profile::firstOrNew(['user_id' => $user->id]);

        return view('profile.index', compact('profile'));
    }

    // プロフィール編集画面表示
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
            'address' => 'nullable|string|max:255',
        ]);

        $user = Auth::user();

        Profile::updateOrCreate(
            ['user_id' => $user->id],
            [
                'nickname' => $request->nickname,
                'address' => $request->address,
            ]
        );

        // ✅ 更新後にトップページに遷移
        return redirect('/')->with('success', 'プロフィールを更新しました。');
    }
}
