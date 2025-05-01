<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Usuario;
use Illuminate\Support\Facades\Hash;

class GenerarClientes extends Command
{
    protected $signature = 'generar:clientes {cantidad=20}';
    protected $description = 'Genera usuarios con rol de cliente para pruebas';

    public function handle()
    {
        $cantidad = (int) $this->argument('cantidad');

        for ($i = 0; $i < $cantidad; $i++) {
            $usuario = Usuario::create([
                'nombre' => 'Cliente' . $i,
                'apellido' => 'Prueba',
                'email' => "cliente{$i}@test.com",
		'password' => Hash::make('password')
            ]);

            User::create([
                'usuario_id' => $usuario->id,
                'email' => $usuario->email,
                'password' => Hash::make('password'), // Contraseña por defecto
                'rol' => 'cliente',
                'estado' => 'activo',
            ]);
        }

        $this->info("Se han creado {$cantidad} clientes correctamente.");
    }
}
