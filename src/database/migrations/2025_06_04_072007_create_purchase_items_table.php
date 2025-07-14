<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('purchase_items', function (Blueprint $table) {
            $table->id();

            // 外部キー制約付きのリレーション
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('item_id')->constrained()->onDelete('cascade');

            // 購入時の住所と購入日時
            $table->string('address')->nullable();
            $table->timestamp('purchased_at')->nullable();

            $table->timestamps();

            // 重複購入を防ぐユニーク制約（1ユーザー1商品）
            $table->unique(['user_id', 'item_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_items');
    }
};
