<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('reservas_clases', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('usuario_id');
            $table->unsignedInteger('clase_id');
            $table->timestamp('fecha_registro')->useCurrent();

            $table->foreign('usuario_id')->references('id')->on('usuarios');
            $table->foreign('clase_id')->references('id')->on('clases');
        });
    }
    public function down()
    {
        Schema::dropIfExists('reservas_clases');
    }
};
