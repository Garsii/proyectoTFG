<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
public function up()
{
   Schema::table('tarjetas', function (Blueprint $table) {
        // sólo añade si no existe
        if (! Schema::hasColumn('tarjetas', 'usuario_id')) {
            $table->unsignedInteger('usuario_id')->nullable()->after('uid');
            $table->foreign('usuario_id')
                  ->references('id')->on('usuarios')
                  ->onDelete('set null');
        }
    });
}

public function down()
{
    Schema::table('tarjetas', function (Blueprint $table) {
        $table->dropForeign(['usuario_id']);
        $table->dropColumn('usuario_id');
    });
}

};
