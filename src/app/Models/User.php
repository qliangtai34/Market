<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens; // API認証が必要な場合のみ

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * 一括代入可能な属性
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * 非表示にする属性
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * キャスト設定
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * 出品した商品（1対多）
     */
    public function items()
    {
        return $this->hasMany(Item::class, 'user_id');
    }

    /**
     * いいねした商品（多対多）
     */
    public function likes()
    {
        return $this->belongsToMany(Item::class, 'likes')->withTimestamps();
    }

    /**
     * 購入した商品（多対多）
     */
    public function purchases()
    {
        return $this->belongsToMany(Item::class, 'purchase_items')->withTimestamps();
    }

    public function comments()
{
    return $this->hasMany(Comment::class);
}

}
