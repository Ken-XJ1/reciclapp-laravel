<?php

use Illuminate\Support\Facades\Route;

// Controladores de usuario
use App\Http\Controllers\PremioController;
use App\Http\Controllers\Panel\PremioController as PanelPremioController;
use App\Http\Controllers\ReciclajeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PanelUsuarioController;

// Controladores de admin
use App\Http\Controllers\AdminController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\PropuestaController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\AuditoriaController;
use App\Http\Controllers\ActividadController;
use App\Http\Controllers\LogroController;
use App\Http\Controllers\EntregaReciclajeAdminController;

Route::view('/', 'index')->name('home');

// =================== AUTENTICACIÓN ===================
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

// =================== PANEL USUARIO ===================
Route::prefix('panel')->group(function () {
    Route::get('/', [PanelUsuarioController::class, 'dashboard'])->name('panel');
    Route::view('/mapa', 'panel.mapa')->name('panel.mapa');
    Route::view('/reportes', 'panel.reportes')->name('panel.reportes');
    Route::view('/logros', 'panel.logros')->name('panel.logros');

    // Actividades / eventos para el usuario
    Route::get('/actividades', [ActividadController::class, 'indexPublic'])->name('panel.actividades');

    Route::get('/reciclaje', [ReciclajeController::class, 'index'])->name('panel.reciclaje');
    Route::post('/reciclaje/guardar', [ReciclajeController::class, 'guardar'])->name('panel.reciclaje.guardar');
    Route::get('/reciclaje/proponer', [ReciclajeController::class, 'proponer'])->name('panel.reciclaje.proponer');
    Route::post('/reciclaje/proponer', [ReciclajeController::class, 'guardarPropuesta'])->name('panel.reciclaje.proponer.guardar');
    Route::get('/reciclaje/reportar', [ReciclajeController::class, 'reportar'])->name('panel.reciclaje.reportar');
    Route::post('/reciclaje/reportar', [ReciclajeController::class, 'guardarReporte'])->name('panel.reciclaje.reportar.guardar');
    Route::get('/reciclaje/registrar', [ReciclajeController::class, 'registrar'])->name('panel.reciclaje.registrar');

    Route::get('/premios', [PanelPremioController::class, 'index'])->name('panel.premios');
    Route::post('/premios/{id}/canjear', [PanelPremioController::class, 'canjear'])->name('panel.premios.canjear');
});

// =================== ADMIN ===================
Route::prefix('admin')->group(function () {
    // Dashboard principal del administrador
    Route::get('/', [AdminController::class, 'dashboard'])->name('admin.dashboard');

    // Usuarios
    Route::get('/usuarios', [UsuarioController::class, 'index'])->name('admin.usuarios');
    Route::get('/usuarios/{id}', [UsuarioController::class, 'show'])->name('admin.usuario.detalle');
    Route::delete('/usuarios/{id}', [UsuarioController::class, 'destroy'])->name('admin.usuario.eliminar');
    Route::patch('/usuarios/{id}/rol', [UsuarioController::class, 'updateRole'])->name('admin.usuario.rol');

    // Premios
    Route::get('/premios', [PremioController::class, 'index'])->name('admin.premios');
    Route::get('/premios/{id}', [PremioController::class, 'show'])->name('admin.premio.detalle');
    Route::post('/premios', [PremioController::class, 'store'])->name('admin.premio.store');
    Route::put('/premios/{id}', [PremioController::class, 'update'])->name('admin.premio.update');
    Route::delete('/premios/{id}', [PremioController::class, 'destroy'])->name('admin.premio.destroy');
    Route::post('/canjes/{id}/gestionar', [PremioController::class, 'gestionarCanje'])->name('admin.canje.gestionar');

    // Propuestas
    Route::get('/propuestas', [PropuestaController::class, 'index'])->name('admin.propuestas');
    Route::post('/propuestas/{id}/aprobar', [PropuestaController::class, 'aprobar'])->name('admin.propuestas.aprobar');
    Route::post('/propuestas/{id}/rechazar', [PropuestaController::class, 'rechazar'])->name('admin.propuestas.rechazar');

    // Reportes
    Route::get('/reportes', [ReporteController::class, 'index'])->name('admin.reportes');
    Route::get('/reportes/{id}', [ReporteController::class, 'show'])->name('admin.reporte.detalle');
    Route::post('/reportes/{id}/resolver', [ReporteController::class, 'resolver'])->name('admin.reporte.resolver');

    // Auditoría
    Route::get('/auditoria', [AuditoriaController::class, 'index'])->name('admin.auditoria');

    // Actividades
    Route::get('/actividades', [ActividadController::class, 'index'])->name('admin.actividades');
    Route::post('/actividades', [ActividadController::class, 'store'])->name('admin.actividades.store');
    Route::put('/actividades/{id}', [ActividadController::class, 'update'])->name('admin.actividades.update');
    Route::delete('/actividades/{id}', [ActividadController::class, 'destroy'])->name('admin.actividades.destroy');

    // Logros
    Route::get('/logros', [LogroController::class, 'index'])->name('admin.logros');
    Route::post('/logros', [LogroController::class, 'store'])->name('admin.logros.store');
    Route::put('/logros/{id}', [LogroController::class, 'update'])->name('admin.logros.update');
    Route::delete('/logros/{id}', [LogroController::class, 'destroy'])->name('admin.logros.destroy');

    // Entregas de reciclaje (validación de puntos)
    Route::get('/entregas', [EntregaReciclajeAdminController::class, 'index'])->name('admin.entregas');
    Route::post('/entregas/{id}/gestionar', [EntregaReciclajeAdminController::class, 'gestionar'])->name('admin.entregas.gestionar');
});
