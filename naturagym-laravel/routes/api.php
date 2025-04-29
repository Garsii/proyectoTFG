<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\NfcController;
use App\Http\Controllers\Api\AccesoController;

// LECTURA NFC (opción “lectura”)
Route::post('lectura', [NfcController::class, 'store']);

// REGISTRO de acceso
Route::post('registro-acceso', [AccesoController::class, 'registrar']);

Route::get('ping', fn() => response()->json(['pong'=>true]));
