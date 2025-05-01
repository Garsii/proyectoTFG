<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Usuario;
use Illuminate\Support\Facades\Hash;

class GenerarClientes extends Command
{
    protected $signature = 'generar:clientes {cantidad=20}';
    protected $description = 'Genera usuarios con rol de cliente (rol "usuario") para pruebas';

    public function handle()
    {
        $cantidad = (int)$this->argument('cantidad');

        for ($i = 0; $i < $cantidad; $i++) {
            Usuario::create([
                'nombre'       => 'Cliente' . $i,
                'apellido'     => 'Prueba',
                'email'        => "cliente{$i}@test.com",
                'password'     => Hash::make('password'), 
                'rol'          => 'usuario',     // <-- aquí forzamos “usuario”
                'estado'       => 'activo',
                'puesto_id'    => null,          // si lo necesitas
            ]);
        }

        $this->info("Se han creado {$cantidad} usuarios con rol 'usuario'.");
    }
}
