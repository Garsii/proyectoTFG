<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('rutinas_usuario', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('usuario_id');
            $table->unsignedInteger('rutina_id');
            $table->string('titulo', 100);
            $table->text('descripcion');
            $table->integer('duracion')->nullable();
            $table->enum('nivel', ['principiante','intermedio','avanzado']);
            $table->string('url_video', 255)->nullable();
            $table->timestamp('fecha_modificacion')->useCurrent()->onUpdate('current_timestamp');

            $table->foreign('usuario_id')->references('id')->on('usuarios');
            $table->foreign('rutina_id')->references('id')->on('rutinas');
        });
    }
    public function down()
    {
        Schema::dropIfExists('rutinas_usuario');
    }
};
