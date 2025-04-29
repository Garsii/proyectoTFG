<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('rutinas', function (Blueprint $table) {
            $table->increments('id');
            $table->string('titulo', 100);
            $table->text('descripcion');
            $table->integer('duracion')->nullable();
            $table->enum('nivel', ['principiante','intermedio','avanzado']);
            $table->string('url_video', 255)->nullable();
            $table->timestamp('fecha_registro')->useCurrent();
        });
    }
    public function down()
    {
        Schema::dropIfExists('rutinas');
    }
};
