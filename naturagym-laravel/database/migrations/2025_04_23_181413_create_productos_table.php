<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('productos', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nombre', 100);
            $table->enum('categoria', ['suplementos','equipo','merchandising','otros']);
            $table->text('descripcion')->nullable();
            $table->decimal('precio', 10, 2);
            $table->integer('stock')->default(0);
            $table->timestamp('fecha_registro')->useCurrent();
        });
    }
    public function down()
    {
        Schema::dropIfExists('productos');
    }
};
