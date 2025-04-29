<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('registros', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('usuario_id')->nullable();
            $table->foreign('usuario_id')->references('id')->on('usuarios')->onDelete('set null');
            $table->unsignedBigInteger('tarjeta_id')->nullable();
            $table->foreign('tarjeta_id')->references('id')->on('tarjetas')->onDelete('set null');
            $table->unsignedBigInteger('punto_acceso_id')->nullable();
            $table->foreign('punto_acceso_id')->references('id')->on('puntos_acceso')->onDelete('set null');
            $table->timestamp('fecha')->useCurrent();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('registros');
    }
};
