<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('registros', function (Blueprint $table) {
            $table->unsignedInteger('usuario_id')->nullable();

            // Asegúrate de que 'usuarios' existe y que la columna 'id' es unsignedBigInteger
            $table->foreign('usuario_id')->references('id')->on('usuarios')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('registros', function (Blueprint $table) {
            $table->dropForeign(['usuario_id']);
            $table->dropColumn('usuario_id');
        });
    }
};
