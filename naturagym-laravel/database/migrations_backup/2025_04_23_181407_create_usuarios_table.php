<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

public function up()
{
    // Verifica si la tabla ya existe antes de intentar crearla
    if (!Schema::hasTable('usuarios')) {
        Schema::create('usuarios', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 50);
            $table->string('apellido', 50);
            $table->string('email', 100)->unique();
            $table->string('password');
            $table->enum('rol', ['usuario', 'admin'])->default('usuario');
            $table->timestamp('fecha_registro')->default(DB::raw('CURRENT_TIMESTAMP'));
            $table->enum('estado', ['activo', 'revocado'])->default('activo');
            $table->unsignedBigInteger('puesto_id')->nullable();
            $table->foreign('puesto_id')->references('id')->on('puestos')->onDelete('set null');
        });
    }
}

    public function down()
    {
        Schema::dropIfExists('usuarios');
    }
};
