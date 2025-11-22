@extends('layouts.admin_layout')

@section('title', 'Reportes de recolección')

@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-xl font-semibold tracking-tight flex items-center gap-2">
            <i class="fas fa-flag"></i>
            <span>Reportes de recolección</span>
        </h1>
        <p class="text-sm text-slate-400 mt-1">Listado de reportes enviados por los usuarios para seguimiento y resolución.</p>
    </div>
    @if(session('success'))
        <div class="px-3 py-2 rounded-lg bg-emerald-500/10 border border-emerald-500/40 text-xs text-emerald-200">
            {{ session('success') }}
        </div>
    @endif
</div>

<div class="rounded-2xl bg-slate-900/70 border border-slate-800 overflow-hidden">
    <table class="min-w-full text-sm">
        <thead class="bg-slate-900/80 border-b border-slate-800">
            <tr>
                <th class="px-4 py-3 text-left text-xs font-medium text-slate-400">ID</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-slate-400">Usuario</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-slate-400">Punto</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-slate-400">Fecha</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-slate-400">Estado</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-slate-400">Observaciones</th>
                <th class="px-4 py-3 text-right text-xs font-medium text-slate-400">Acciones</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-800">
            @forelse($reportes as $reporte)
                <tr class="hover:bg-slate-900/60">
                    <td class="px-4 py-3 align-top text-slate-200 text-xs">{{ (string)($reporte->_id ?? $reporte->id) }}</td>
                    <td class="px-4 py-3 align-top text-slate-200 text-xs">{{ $reporte->id_usuario ?? 'N/D' }}</td>
                    <td class="px-4 py-3 align-top text-slate-200 text-xs">{{ $reporte->id_punto ?? 'N/D' }}</td>
                    <td class="px-4 py-3 align-top text-slate-200 text-xs">
                        {{ optional($reporte->fecha_reporte)->format('d/m/Y H:i') ?? ($reporte->fecha_reporte ?? 'N/D') }}
                    </td>
                    <td class="px-4 py-3 align-top">
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-[11px] font-medium
                            @class([
                                'bg-emerald-500/10 text-emerald-200 border border-emerald-500/40' => ($reporte->estado ?? '') === 'resuelto',
                                'bg-amber-500/10 text-amber-200 border border-amber-500/40' => ($reporte->estado ?? '') === 'en_progreso',
                                'bg-rose-500/10 text-rose-200 border border-rose-500/40' => ($reporte->estado ?? '') === 'pendiente',
                                'bg-slate-500/10 text-slate-200 border border-slate-600/40' => ! in_array($reporte->estado, ['pendiente','en_progreso','resuelto']),
                            ])">
                            {{ ucfirst($reporte->estado ?? 'pendiente') }}
                        </span>
                    </td>
                    <td class="px-4 py-3 align-top text-xs text-slate-300">
                        <span title="{{ $reporte->observaciones ?? 'Sin observaciones' }}">
                            {{ \Illuminate\Support\Str::limit($reporte->observaciones ?? 'Sin observaciones', 60) }}
                        </span>
                    </td>
                    <td class="px-4 py-3 align-top">
                        <div class="flex items-center justify-end gap-2 text-xs">
                            <a href="{{ route('admin.reporte.detalle', $reporte->_id ?? $reporte->id) }}" class="px-3 py-1 rounded-lg bg-slate-800 hover:bg-slate-700 border border-slate-700 inline-flex items-center gap-1">
                                <i class="fas fa-eye"></i>
                                <span>Ver</span>
                            </a>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="px-4 py-6 text-center text-sm text-slate-500">No hay reportes registrados.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
