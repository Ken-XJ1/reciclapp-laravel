@extends('layouts.admin_layout')

@section('title', 'Auditoría del sistema')

@section('content')
    <div class="p-6 space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold tracking-tight">Auditoría del sistema</h1>
                <p class="mt-1 text-sm text-slate-400">Revisa las acciones importantes realizadas por administradores y procesos del sistema.</p>
            </div>
        </div>

        <div class="bg-slate-900/60 border border-slate-800 rounded-xl overflow-hidden">
            <div class="flex items-center justify-between px-4 py-3 border-b border-slate-800">
                <h2 class="text-sm font-semibold text-slate-200">Últimos registros ({{ $logs->count() }})</h2>
                <p class="text-[11px] text-slate-500">Mostrando hasta los 100 registros más recientes</p>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full text-xs md:text-sm">
                    <thead class="bg-slate-900/80">
                        <tr class="text-left text-slate-400">
                            <th class="px-3 py-2">Fecha</th>
                            <th class="px-3 py-2">Usuario</th>
                            <th class="px-3 py-2">Acción</th>
                            <th class="px-3 py-2">Módulo</th>
                            <th class="px-3 py-2">Resultado</th>
                            <th class="px-3 py-2">IP</th>
                            <th class="px-3 py-2">Descripción</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logs as $log)
                            <tr class="border-t border-slate-800/80 hover:bg-slate-900/40">
                                <td class="px-3 py-2 align-top text-slate-300 whitespace-nowrap">
                                    @if($log->fecha)
                                        {{ \Carbon\Carbon::parse($log->fecha)->format('d/m/Y H:i:s') }}
                                    @endif
                                </td>
                                <td class="px-3 py-2 align-top text-slate-200">
                                    {{ $log->usuario ?? 'N/A' }}
                                </td>
                                <td class="px-3 py-2 align-top text-slate-100">
                                    {{ $log->accion ?? '-' }}
                                </td>
                                <td class="px-3 py-2 align-top text-slate-300">
                                    {{ $log->modulo_afectado ?? '-' }}
                                </td>
                                <td class="px-3 py-2 align-top">
                                    @php $resultado = $log->resultado ?? 'info'; @endphp
                                    @if(strtolower($resultado) === 'exitoso' || strtolower($resultado) === 'ok')
                                        <span class="inline-flex rounded-full bg-emerald-500/20 px-2 py-0.5 text-[11px] font-medium text-emerald-300">{{ $resultado }}</span>
                                    @elseif(strtolower($resultado) === 'error' || strtolower($resultado) === 'fallido')
                                        <span class="inline-flex rounded-full bg-red-500/20 px-2 py-0.5 text-[11px] font-medium text-red-300">{{ $resultado }}</span>
                                    @else
                                        <span class="inline-flex rounded-full bg-slate-700/40 px-2 py-0.5 text-[11px] font-medium text-slate-200">{{ $resultado }}</span>
                                    @endif
                                </td>
                                <td class="px-3 py-2 align-top text-slate-300 whitespace-nowrap">{{ $log->ip ?? '-' }}</td>
                                <td class="px-3 py-2 align-top text-slate-300 max-w-md">
                                    {{ \Illuminate\Support\Str::limit($log->descripcion ?? '-', 120) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-3 py-6 text-center text-sm text-slate-500">No se encontraron registros de auditoría.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

