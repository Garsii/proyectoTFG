<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('registros', function (Blueprint $table) {
            // 1) Borramos la columna si existe (aunque no tenga FK)
            if (Schema::hasColumn('registros','tarjeta_id')) {
                $table->dropColumn('tarjeta_id');
            }

            // 2) Creamos la columna con el tipo correcto
            $table->unsignedInteger('tarjeta_id')->nullable()->after('usuario_id');

            // 3) Creamos la FK ahora sí bien hecha
            $table->foreign('tarjeta_id')
                  ->references('id')->on('tarjetas')
                  ->onDelete('set null');
        });
    }

    public function down()
    {
        Schema::table('registros', function (Blueprint $table) {
            $table->dropForeign(['tarjeta_id']);
            $table->dropColumn('tarjeta_id');
        });
    }
};
