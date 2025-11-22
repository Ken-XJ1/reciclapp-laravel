@extends('layouts.admin_layout')

@section('title', 'Detalle de reporte')

@section('content')
<div class="mb-6 flex items-center justify-between">
    <div>
        <h1 class="text-xl font-semibold tracking-tight flex items-center gap-2">
            <i class="fas fa-flag"></i>
            <span>Detalle de reporte</span>
        </h1>
        <p class="text-sm text-slate-400 mt-1">Revisa la información del reporte y actualiza su estado.</p>
    </div>
    <a href="{{ route('admin.reportes') }}" class="inline-flex items-center gap-2 px-3 py-2 rounded-lg bg-slate-800 hover:bg-slate-700 text-xs font-medium">
        <i class="fas fa-arrow-left"></i>
        <span>Volver a la lista</span>
    </a>
</div>

@if(!$reporte)
    <div class="rounded-2xl bg-slate-900/70 border border-rose-500/40 px-4 py-3 text-sm text-rose-200">
        El reporte solicitado no existe.
    </div>
@else
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6 text-sm">
        <div class="rounded-2xl bg-slate-900/70 border border-slate-800 p-4 space-y-2">
            <h2 class="text-sm font-semibold mb-2">Información general</h2>
            <p><span class="text-slate-400">ID:</span> <span class="text-slate-100">{{ (string)($reporte->_id ?? $reporte->id) }}</span></p>
            <p><span class="text-slate-400">Usuario:</span> <span class="text-slate-100">{{ $reporte->id_usuario ?? 'N/D' }}</span></p>
            <p><span class="text-slate-400">Punto:</span> <span class="text-slate-100">{{ $reporte->id_punto ?? 'N/D' }}</span></p>
            <p><span class="text-slate-400">Fecha de reporte:</span>
                <span class="text-slate-100">
                    {{ optional($reporte->fecha_reporte)->format('d/m/Y H:i') ?? ($reporte->fecha_reporte ?? 'N/D') }}
                </span>
            </p>
        </div>

        <div class="rounded-2xl bg-slate-900/70 border border-slate-800 p-4 space-y-2">
            <h2 class="text-sm font-semibold mb-2">Estado</h2>
            <p class="mb-2">
                <span class="text-slate-400">Estado actual:</span>
                <span class="inline-flex items-center px-2 py-1 rounded-full text-[11px] font-medium ml-1
                    @class([
                        'bg-emerald-500/10 text-emerald-200 border border-emerald-500/40' => ($reporte->estado ?? '') === 'resuelto',
                        'bg-amber-500/10 text-amber-200 border border-amber-500/40' => ($reporte->estado ?? '') === 'en_progreso',
                        'bg-rose-500/10 text-rose-200 border border-rose-500/40' => ($reporte->estado ?? '') === 'pendiente',
                        'bg-slate-500/10 text-slate-200 border border-slate-600/40' => ! in_array($reporte->estado, ['pendiente','en_progreso','resuelto']),
                    ])">
                    {{ ucfirst($reporte->estado ?? 'pendiente') }}
                </span>
            </p>

            <form action="{{ route('admin.reporte.resolver', $reporte->_id ?? $reporte->id) }}" method="POST" class="space-y-3">
                @csrf
                <div>
                    <label for="estado" class="block text-xs text-slate-400 mb-1">Nuevo estado</label>
                    <select id="estado" name="estado" class="w-full rounded-lg bg-slate-950/80 border border-slate-700 text-xs px-3 py-2">
                        <option value="pendiente" @selected(($reporte->estado ?? '') === 'pendiente')>Pendiente</option>
                        <option value="en_progreso" @selected(($reporte->estado ?? '') === 'en_progreso')>En progreso</option>
                        <option value="resuelto" @selected(($reporte->estado ?? '') === 'resuelto')>Resuelto</option>
                    </select>
                </div>

                <button type="submit" class="inline-flex items-center gap-2 px-4 py-2 rounded-lg bg-emerald-500 hover:bg-emerald-400 text-xs font-medium text-slate-950">
                    <i class="fas fa-save"></i>
                    <span>Actualizar estado</span>
                </button>
            </form>
        </div>
    </div>

    <div class="rounded-2xl bg-slate-900/70 border border-slate-800 p-4 text-sm">
        <h2 class="text-sm font-semibold mb-2">Observaciones</h2>
        <p class="text-slate-300 whitespace-pre-line">{{ $reporte->observaciones ?? 'Sin observaciones registradas.' }}</p>
    </div>
@endif
@endsection
