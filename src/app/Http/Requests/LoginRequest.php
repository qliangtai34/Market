<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'email'],
            'password' => ['required', 'min:8'],
        ];
    }

    public function messages(): array
    {
        return [
            // 未入力エラー
            'email.required' => 'メールアドレスを入力してください',
            'password.required' => 'パスワードを入力してください',

            // バリデーション違反
            'email.email' => 'ログイン情報が登録されていません。',
            'password.min' => 'ログイン情報が登録されていません。',
        ];
    }
}
