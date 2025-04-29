<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('dietas', function (Blueprint $table) {
            $table->increments('id');
            $table->string('titulo', 100);
            $table->text('descripcion');
            $table->integer('calorias')->nullable();
            $table->text('recomendaciones')->nullable();
            $table->timestamp('fecha_registro')->useCurrent();
        });
    }
    public function down()
    {
        Schema::dropIfExists('dietas');
    }
};
