<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string'],
            'email' => ['required', 'email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'password_confirmation' => ['required'],
        ];
    }

    public function messages(): array
    {
        return [
            // 未入力の場合
            'name.required' => 'お名前を入力してください',
            'email.required' => 'メールアドレスを入力してください',
            'password.required' => 'パスワードを入力してください',
            'password_confirmation.required' => '確認用パスワードを入力してください',

            // パスワードの入力規則違反
            'password.min' => 'パスワードは8文字以上で入力してください',

            // パスワード確認の一致チェック
            'password.confirmed' => 'パスワードと一致しません',
        ];
    }
}
