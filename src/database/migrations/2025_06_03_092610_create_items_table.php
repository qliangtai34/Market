<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
{
    Schema::create('items', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->integer('price');
        $table->text('description');
        $table->string('img_url');
        $table->string('condition');
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->boolean('is_sold')->default(false);
        $table->timestamps();
    });
}


    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
