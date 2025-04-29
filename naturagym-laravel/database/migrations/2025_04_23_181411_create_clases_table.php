<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('clases', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nombre', 100);
            $table->text('descripcion')->nullable();
            $table->time('hora_inicio');
            $table->time('hora_fin');
            $table->string('instructor', 100)->nullable();
            $table->integer('cupo');
            $table->date('fecha');
            $table->timestamp('fecha_registro')->useCurrent();
        });
    }
    public function down()
    {
        Schema::dropIfExists('clases');
    }
};
