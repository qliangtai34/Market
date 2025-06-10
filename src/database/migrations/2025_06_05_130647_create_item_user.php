<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemUser extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('item_user', function (Blueprint $table) {
            $table->id();

            // 外部キー：user_id（ユーザー）
            $table->unsignedBigInteger('user_id');

            // 外部キー：item_id（商品）
            $table->unsignedBigInteger('item_id');

            // いいねフラグ（true = いいねしている）
            $table->boolean('is_liked')->default(false);

            // 購入済みフラグ（true = 購入済み）
            $table->boolean('is_purchased')->default(false);

            $table->timestamps();

            // 外部キー制約（整合性保持）
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('item_id')->references('id')->on('items')->onDelete('cascade');

            // ユニーク制約（同じ user-item ペアは1件のみ）
            $table->unique(['user_id', 'item_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('item_user');
    }
}
