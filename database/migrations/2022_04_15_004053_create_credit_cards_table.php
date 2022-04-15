<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    const TABLE_NAME = 'credit_cards';

    public function up()
    {
        Schema::create(self::TABLE_NAME, function (Blueprint $table) {
            $table->id();
            $table->string('bank');
            $table->string('flag');
            $table->string('cardholder_name');
            $table->string('number');
            $table->string('cvv', 3);
            $table->string('expiration', 5);
            $table->string('bg_color');
            $table->string('txt_color');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists(self::TABLE_NAME);
    }
};
