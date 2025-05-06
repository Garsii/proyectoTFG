<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('tarjetas', function (Blueprint $table) {
            // Si no existe, agregar usuario_id como BIGINT para coincidir con usuarios.id (BIGINT)
            if (!Schema::hasColumn('tarjetas', 'usuario_id')) {
                $table->unsignedBigInteger('usuario_id')->nullable()->after('uid');
                $table->foreign('usuario_id')->references('id')->on('usuarios')->onDelete('set null');
            }
        });
    }

    public function down(): void
    {
        Schema::table('tarjetas', function (Blueprint $table) {
            $table->dropForeign(['usuario_id']);
            $table->dropColumn('usuario_id');
        });
    }
};
