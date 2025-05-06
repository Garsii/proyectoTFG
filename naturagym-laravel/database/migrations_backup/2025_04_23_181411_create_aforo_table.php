<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('aforo', function (Blueprint $table) {
            $table->increments('id');
            $table->date('fecha');
            $table->time('hora');
            $table->integer('aforo');
            $table->timestamp('fecha_registro')->useCurrent();
        });
    }
    public function down()
    {
        Schema::dropIfExists('aforo');
    }
};
