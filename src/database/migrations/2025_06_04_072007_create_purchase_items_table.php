<?php

// database/migrations/xxxx_xx_xx_create_purchase_items_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePurchaseItemsTable extends Migration
{
    public function up()
    {
        Schema::create('purchase_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('item_id')->constrained()->onDelete('cascade');
            $table->string('address')->nullable();  // 住所カラムを追加
            $table->timestamp('purchased_at')->nullable();  // 購入日時カラムを追加
            $table->timestamps();

            $table->unique(['user_id', 'item_id']);  // ユーザーと商品で重複購入不可など
        });
    }

    public function down()
    {
        Schema::dropIfExists('purchase_items');
    }
}
