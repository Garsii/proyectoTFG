<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('tarjetas', function (Blueprint $table) {
            $table->increments('id');
            $table->string('uid', 20)->unique();
            $table->timestamps();
        });
    }
    public function down()
    {
        Schema::dropIfExists('tarjetas');
    }
};
