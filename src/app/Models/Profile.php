<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Profile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'nickname',
        'zipcode',
        'address',
        'building',
        'image_path',
    ];

    /**
     * ユーザーとのリレーション（1対1）
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * プロフィール画像URLを返す（ストレージ or デフォルト画像）
     */
    public function getImageUrlAttribute()
    {
        if ($this->image_path && Storage::disk('public')->exists($this->image_path)) {
            return asset('storage/' . $this->image_path);
        }

        return asset('images/default_avatar.png');
    }
}
