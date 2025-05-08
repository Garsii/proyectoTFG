<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\TarjetaController;
use App\Http\Controllers\Admin\UsuarioController;
use App\Http\Controllers\Admin\RegistroController;
use App\Http\Controllers\ProfileController;

// ✱ Raíz: redirige siempre al dashboard
Route::get('/', fn() => redirect()->route('dashboard'));

// Dashboard genérico que redirige según rol
Route::get('/dashboard', function () {
    if (auth()->user()->rol === 'admin') {
        return redirect()->route('admin.usuarios.index');
    }
    return view('dashboard');
})->middleware(['auth','verified'])->name('dashboard');

// ── Panel de Administración ─────────────────────────────────────────
Route::middleware(['auth','verified'])
     ->prefix('admin')
     ->name('admin.')
     ->group(function(){

    // Dashboard admin → listado de usuarios
    Route::get('/dashboard', fn() => redirect()->route('admin.usuarios.index'))
         ->name('dashboard');

    // Listado + bulk-update
    Route::get('usuarios', [UsuarioController::class,'index'])
         ->name('usuarios.index');
    Route::patch('usuarios', [UsuarioController::class,'bulkUpdate'])
         ->name('usuarios.bulkUpdate');

    // Edición individual (opcional)
    Route::get('usuarios/{id}/edit', [UsuarioController::class,'edit'])
         ->name('usuarios.edit');
    Route::post('usuarios/{id}/actualizar', [UsuarioController::class,'update'])
         ->name('usuarios.update');

    // Logs y eliminación
    Route::get('usuarios/{id}/logs', [UsuarioController::class,'logs'])
         ->name('usuarios.logs');
    Route::delete('usuarios/{id}', [UsuarioController::class,'destroy'])
         ->name('usuarios.destroy');

    // NFC cards & registros (sin cambios)
    Route::resource('tarjetas', TarjetaController::class);
    Route::resource('registros', RegistroController::class);
});

// ── Perfil de usuario ────────────────────────────────────────────────
Route::middleware('auth')
     ->prefix('user')
     ->group(function(){
    Route::get('profile',   [ProfileController::class,'edit'])->name('profile.edit');
    Route::patch('profile', [ProfileController::class,'update'])->name('profile.update');
    Route::delete('profile',[ProfileController::class,'destroy'])->name('profile.destroy');
});

Route::get('/test-mail', function () {
    try {
        Mail::raw('Este es un correo de prueba desde Laravel.', function ($msg) {
            $msg->to('admin@tfgmail.alvaroasir.com')
                ->subject('Correo de prueba');
        });

        return 'Correo enviado correctamente.';
    } catch (\Throwable $e) {
        return 'Error al enviar correo: ' . $e->getMessage();
    }
});
require __DIR__.'/auth.php';
