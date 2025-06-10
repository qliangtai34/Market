<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Item;

class Category extends Model
{
    use HasFactory;

    // 一括代入可能な属性
    protected $fillable = ['name'];

    /**
     * 商品とのリレーション（多対多）
     */
    public function items()
    {
        return $this->belongsToMany(Item::class, 'item_category');
        // 中間テーブル名が 'item_category' であれば明示指定
    }
}
