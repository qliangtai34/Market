<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Category;
use App\Models\Comment;

class Item extends Model
{
    use HasFactory;

    /**
     * 一括代入可能な属性
     */
    protected $fillable = [
        'name',
        'price',
        'description',
        'img_url',
        'condition',
        'user_id',    // 出品者ID
        'is_sold',    // 購入済みフラグ
        'brand',      // ブランド名
    ];

    /**
     * 型変換
     */
    protected $casts = [
        'is_sold' => 'boolean',
        'price' => 'integer',
    ];

    /**
     * 出品者（1対多の逆リレーション）
     */
    public function seller()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * いいねしたユーザー（多対多）
     */
    public function likedUsers()
    {
        return $this->belongsToMany(User::class, 'likes')
                    ->withTimestamps();
    }

    /**
     * コメント一覧（1対多）
     */
    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    /**
     * 商品カテゴリ（多対多）
     */
    public function categories()
    {
        return $this->belongsToMany(Category::class, 'item_category')
                    ->withTimestamps();
    }

    /**
     * 購入者（多対多）＋中間テーブル情報
     */
    public function buyers()
    {
        return $this->belongsToMany(User::class, 'purchase_items')
                    ->withTimestamps()
                    ->withPivot('address', 'purchased_at');
    }
}
