<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('registros', function (Blueprint $table) {
            $table->id();
            // Se define usuario_id como BIGINT para usuarios.id
            $table->unsignedBigInteger('usuario_id')->nullable();
            // tarjeta_id e punto_acceso_id como INTEGER para coincidir con sus tablas
            $table->unsignedInteger('tarjeta_id')->nullable();
            $table->unsignedInteger('punto_acceso_id')->nullable();
            $table->timestamp('fecha')->useCurrent();
            // Se agrega el campo 'acceso' aquí en lugar de migración separada
            $table->enum('acceso', ['permitido', 'denegado'])->default('denegado');
            $table->timestamps();

            $table->foreign('usuario_id')->references('id')->on('usuarios')->onDelete('set null');
            $table->foreign('tarjeta_id')->references('id')->on('tarjetas')->onDelete('set null');
            $table->foreign('punto_acceso_id')->references('id')->on('puntos_acceso')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('registros');
    }
};
