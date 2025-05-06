<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('registros', function (Blueprint $table) {
            // 1) Si existe, eliminar constraint y columna equivocada
            if (Schema::hasColumn('registros','tarjeta_id')) {
                $table->dropForeign(['tarjeta_id']);    // quita FK
                $table->dropColumn('tarjeta_id');      // quita la columna
            }
            // 2) Volver a crear tarjeta_id con el tipo correcto
            $table->unsignedInteger('tarjeta_id')->nullable()->after('usuario_id');
            // 3) Crear la foreign key apuntando a tarjetas.id
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
