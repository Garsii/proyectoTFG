<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UsuariosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear un usuario admin
        DB::table('usuarios')->insert([
            'nombre' => 'Admin',
            'apellido' => 'User',
            'email' => 'admin@naturagym.com',
            'password' => bcrypt('password123'),  // Cambia por el hash real
            'rol' => 'admin',
            'estado' => 'activo',
            'fecha_registro' => now(),
            'puesto_id' => null,  // o el id del puesto que prefieras
        ]);
    }
}
