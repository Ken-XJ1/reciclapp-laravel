@extends('layouts.admin_layout')

@section('title', 'Dashboard')

@section('content')
<div class="flex items-start justify-between mb-8">
    <div>
        <p class="text-sm text-slate-400 mb-1">Panel de administración</p>
        <h1 class="text-2xl md:text-3xl font-semibold tracking-tight">Hola, {{ auth()->user()->nombre ?? 'Administrador' }}</h1>
        <p class="text-sm text-slate-400 mt-2 max-w-xl">
            Desde aquí puedes monitorear la actividad de ReciclApp, gestionar usuarios, puntos de reciclaje, reportes y recompensas.
        </p>
    </div>
    <div class="hidden sm:flex items-center gap-3">
        <a href="{{ route('admin.reportes') }}" class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-slate-800 hover:bg-slate-700 text-xs font-medium">
            <i class="fas fa-flag"></i>
            <span>Ver reportes</span>
        </a>
        <a href="{{ route('admin.premios') }}" class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-emerald-500 hover:bg-emerald-400 text-xs font-medium text-slate-950">
            <i class="fas fa-gift"></i>
            <span>Gestionar premios</span>
        </a>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-5 mb-8">
    <div class="rounded-2xl bg-slate-900/70 border border-slate-800 p-4 flex flex-col gap-1">
        <div class="flex items-center justify-between">
            <span class="text-xs uppercase tracking-wide text-slate-400">Usuarios</span>
            <i class="fas fa-users text-emerald-400"></i>
        </div>
        <p class="text-2xl font-semibold mt-1">{{ \App\Models\Usuario::count() }}</p>
        <p class="text-xs text-slate-500">Registrados en la plataforma</p>
    </div>

    <div class="rounded-2xl bg-slate-900/70 border border-slate-800 p-4 flex flex-col gap-1">
        <div class="flex items-center justify-between">
            <span class="text-xs uppercase tracking-wide text-slate-400">Puntos de reciclaje</span>
            <i class="fas fa-map-marker-alt text-sky-400"></i>
        </div>
        <p class="text-2xl font-semibold mt-1">{{ \App\Models\PuntoReciclaje::count() }}</p>
        <p class="text-xs text-slate-500">Centros registrados</p>
    </div>

    <div class="rounded-2xl bg-slate-900/70 border border-slate-800 p-4 flex flex-col gap-1">
        <div class="flex items-center justify-between">
            <span class="text-xs uppercase tracking-wide text-slate-400">Reportes</span>
            <i class="fas fa-flag text-rose-400"></i>
        </div>
        <p class="text-2xl font-semibold mt-1">{{ \App\Models\ReporteRecoleccion::count() }}</p>
        <p class="text-xs text-slate-500">Reportes registrados</p>
    </div>

    <div class="rounded-2xl bg-slate-900/70 border border-slate-800 p-4 flex flex-col gap-1">
        <div class="flex items-center justify-between">
            <span class="text-xs uppercase tracking-wide text-slate-400">Premios</span>
            <i class="fas fa-gift text-amber-300"></i>
        </div>
        <p class="text-2xl font-semibold mt-1">{{ \App\Models\Premio::count() }}</p>
        <p class="text-xs text-slate-500">Disponibles para canje</p>
    </div>
</div>

<div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
    <div class="xl:col-span-2 rounded-2xl bg-slate-900/70 border border-slate-800 p-5">
        <h2 class="text-sm font-semibold mb-2">Atajos rápidos</h2>
        <p class="text-sm text-slate-400 mb-4">Elige una acción para gestionar los recursos principales de ReciclApp.</p>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
            <a href="{{ route('admin.usuarios') }}" class="inline-flex items-center justify-between px-4 py-3 rounded-xl bg-slate-950/60 border border-slate-800 hover:border-emerald-500/60 transition">
                <div>
                    <p class="font-medium">Gestionar usuarios</p>
                    <p class="text-xs text-slate-500">Ver, editar o desactivar cuentas</p>
                </div>
                <i class="fas fa-users"></i>
            </a>
            <a href="{{ route('admin.propuestas') }}" class="inline-flex items-center justify-between px-4 py-3 rounded-xl bg-slate-950/60 border border-slate-800 hover:border-sky-500/60 transition">
                <div>
                    <p class="font-medium">Revisar propuestas</p>
                    <p class="text-xs text-slate-500">Nuevos puntos de reciclaje sugeridos</p>
                </div>
                <i class="fas fa-map-marker-alt"></i>
            </a>
            <a href="{{ route('admin.reportes') }}" class="inline-flex items-center justify-between px-4 py-3 rounded-xl bg-slate-950/60 border border-slate-800 hover:border-rose-500/60 transition">
                <div>
                    <p class="font-medium">Gestionar reportes</p>
                    <p class="text-xs text-slate-500">Incidencias enviadas por los usuarios</p>
                </div>
                <i class="fas fa-flag"></i>
            </a>
            <a href="{{ route('admin.premios') }}" class="inline-flex items-center justify-between px-4 py-3 rounded-xl bg-slate-950/60 border border-slate-800 hover:border-amber-400/70 transition">
                <div>
                    <p class="font-medium">Configurar premios</p>
                    <p class="text-xs text-slate-500">Crear y actualizar recompensas</p>
                </div>
                <i class="fas fa-gift"></i>
            </a>
        </div>
    </div>

    <div class="rounded-2xl bg-slate-900/70 border border-slate-800 p-5 text-sm">
        <h2 class="text-sm font-semibold mb-2">Resumen rápido</h2>
        <p class="text-slate-400 mb-4">Un vistazo general al estado actual de ReciclApp.</p>
        <ul class="space-y-2 text-slate-300">
            <li class="flex items-center justify-between"><span>Usuarios activos</span> <span class="text-slate-400">{{ \App\Models\Usuario::count() }}</span></li>
            <li class="flex items-center justify-between"><span>Puntos de reciclaje</span> <span class="text-slate-400">{{ \App\Models\PuntoReciclaje::count() }}</span></li>
            <li class="flex items-center justify-between"><span>Reportes registrados</span> <span class="text-slate-400">{{ \App\Models\ReporteRecoleccion::count() }}</span></li>
            <li class="flex items-center justify-between"><span>Premios activos</span> <span class="text-slate-400">{{ \App\Models\Premio::count() }}</span></li>
        </ul>
    </div>
</div>
@endsection
