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
        'user_id',   // 出品者ID
        'is_sold',   // 購入済みフラグ（boolean）
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
        return $this->belongsToMany(User::class, 'likes')->withTimestamps();
    }

    /**
     * likes() メソッド（Laravel標準名でのアクセス対応）
     * 他の箇所で `$item->likes` と使っている場合のために定義
     */
    public function likes()
    {
        return $this->belongsToMany(User::class, 'likes')->withTimestamps();
    }

    /**
     * コメント（1対多）
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
        return $this->belongsToMany(Category::class, 'item_category');
    }

    /**
     * 購入者（多対多）
     * purchase_items テーブルを使用
     */
    public function buyers()
    {
        return $this->belongsToMany(User::class, 'purchase_items')
                    ->withTimestamps()
                    ->withPivot('address', 'purchased_at');
    }
}
