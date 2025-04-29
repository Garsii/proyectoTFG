<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAccesoToRegistrosTable extends Migration
{
    public function up()
    {
        Schema::table('registros', function (Blueprint $table) {
            // Añadimos el campo 'acceso' tras 'fecha'
            if (!Schema::hasColumn('registros', 'fecha')) {
            $table->timestamp('fecha')->default(DB::raw('CURRENT_TIMESTAMP'));
        }
        $table->enum('acceso', ['permitido', 'denegado'])->default('denegado')->after('fecha');
    });
    }

    public function down()
    {
        Schema::table('registros', function (Blueprint $table) {
            $table->dropColumn('acceso');
        });
    }
}
