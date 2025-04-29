<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\TarjetaController;
use App\Http\Controllers\Admin\UsuarioController;
use App\Http\Controllers\Admin\RegistroController;
use App\Http\Controllers\ProfileController;

// ✱ Raíz: redirige siempre al dashboard
Route::get('/', function () {
    return redirect()->route('dashboard');
});

// Dashboard genérico que a su vez redirige según rol
Route::get('/dashboard', function () {
    if (auth()->user()->rol === 'admin') {
        return redirect()->route('admin.dashboard');
    }
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// ── Panel de Administración ─────────────────────────────────────────
Route::middleware(['auth', 'verified'])->prefix('admin')->name('admin.')->group(function () {
    // Al entrar al dashboard admin: redirige al listado de usuarios
    Route::get('/dashboard', function () {
        return redirect()->route('admin.usuarios.index');
    })->name('dashboard');

    // CRUD Usuarios
    Route::get('/usuarios', [UsuarioController::class, 'index'])->name('usuarios.index');
    Route::get('/usuarios/{id}/edit', [UsuarioController::class, 'edit'])->name('usuarios.edit');
    Route::post('/usuarios/{id}/actualizar', [UsuarioController::class, 'update'])->name('usuarios.update');
    Route::get('/usuarios/{id}/logs', [UsuarioController::class, 'logs'])->name('usuarios.logs');

    // CRUD Tarjetas NFC
    Route::resource('tarjetas', TarjetaController::class);

    // CRUD Registros de acceso
    Route::resource('registros', RegistroController::class);
});

// ── Rutas de Perfil para usuario autenticado ─────────────────────────
Route::middleware(['auth'])->prefix('user')->group(function () {
    Route::get('profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
