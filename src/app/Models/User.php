<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

use App\Models\Item;
use App\Models\Comment;
use App\Models\Profile;

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
        // 'address', // usersテーブルにaddressカラムがなければコメントアウトまたは削除
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
        return $this->hasMany(Item::class);
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
        return $this->belongsToMany(Item::class, 'purchase_items', 'user_id', 'item_id')
                    ->withTimestamps()
                    ->withPivot('address', 'purchased_at');
    }

    /**
     * コメント（1対多）
     */
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * プロフィール（1対1）
     */
    public function profile()
    {
        return $this->hasOne(Profile::class);
    }
}
