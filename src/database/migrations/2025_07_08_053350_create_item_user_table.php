<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('item_user', function (Blueprint $table) {
            $table->id();

            $table->foreignId('item_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            $table->string('address');       // 送付先住所
            $table->timestamp('purchased_at')->nullable();  // 購入日時

            $table->timestamps();

            // item_id と user_id の組み合わせはユニークにする（同じ購入者が同じ商品を複数回買えない場合）
            $table->unique(['item_id', 'user_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('item_user');
    }
};
