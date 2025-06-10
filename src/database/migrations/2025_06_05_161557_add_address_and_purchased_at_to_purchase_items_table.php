<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAddressAndPurchasedAtToPurchaseItemsTable extends Migration
{
    public function up()
    {
        Schema::table('purchase_items', function (Blueprint $table) {
            $table->string('address')->nullable()->after('item_id');
            $table->timestamp('purchased_at')->nullable()->after('address');
        });
    }

    public function down()
    {
        Schema::table('purchase_items', function (Blueprint $table) {
            $table->dropColumn(['address', 'purchased_at']);
        });
    }
}
