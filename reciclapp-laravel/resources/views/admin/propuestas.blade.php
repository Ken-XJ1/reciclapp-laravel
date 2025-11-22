@extends('layouts.admin_layout')

@section('title', 'Propuestas de puntos de reciclaje')

@section('content')
    <div class="p-6 space-y-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold tracking-tight">Propuestas de nuevos puntos</h1>
                <p class="mt-1 text-sm text-slate-400">Revisa y decide qué puntos de reciclaje sugeridos por usuarios se activan en el sistema.</p>
            </div>
        </div>

        @if(session('success'))
            <div class="rounded-lg border border-emerald-500/40 bg-emerald-500/10 px-4 py-3 text-sm text-emerald-200">
                {{ session('success') }}
            </div>
        @elseif(session('error'))
            <div class="rounded-lg border border-red-500/40 bg-red-500/10 px-4 py-3 text-sm text-red-200">
                {{ session('error') }}
            </div>
        @endif

        <div class="bg-slate-900/60 border border-slate-800 rounded-xl overflow-hidden">
            <div class="flex items-center justify-between px-4 py-3 border-b border-slate-800">
                <h2 class="text-sm font-semibold text-slate-200">Propuestas pendientes de revisión</h2>
            </div>

            <table class="min-w-full text-sm">
                <thead class="bg-slate-900/80">
                    <tr class="text-left text-slate-400">
                        <th class="px-4 py-3">Nombre</th>
                        <th class="px-4 py-3">Tipo</th>
                        <th class="px-4 py-3">Dirección</th>
                        <th class="px-4 py-3">Horario</th>
                        <th class="px-4 py-3">Descripción</th>
                        <th class="px-4 py-3">Propuesto por</th>
                        <th class="px-4 py-3">Fecha propuesta</th>
                        <th class="px-4 py-3 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($propuestas as $propuesta)
                        <tr class="border-t border-slate-800/80 hover:bg-slate-900/40">
                            <td class="px-4 py-3 align-top">
                                <div class="font-medium text-slate-100">{{ $propuesta->nombre }}</div>
                            </td>
                            <td class="px-4 py-3 align-top text-slate-200">{{ $propuesta->tipo }}</td>
                            <td class="px-4 py-3 align-top text-slate-300">{{ $propuesta->direccion }}</td>
                            <td class="px-4 py-3 align-top text-slate-300">{{ $propuesta->horario ?? 'N/A' }}</td>
                            <td class="px-4 py-3 align-top text-xs text-slate-400 max-w-xs">
                                @php
                                    $desc = $propuesta->descripcion ?? '';
                                @endphp
                                {{ \Illuminate\Support\Str::limit($desc, 80) }}
                            </td>
                            <td class="px-4 py-3 align-top text-xs text-slate-300">
                                @if(!empty($propuesta->proponente_nombre))
                                    <div>{{ $propuesta->proponente_nombre }} {{ $propuesta->proponente_apellido }}</div>
                                    <div class="text-[11px] text-slate-400">{{ $propuesta->proponente_email }}</div>
                                @else
                                    <div class="text-slate-400">Usuario desconocido</div>
                                @endif
                            </td>
                            <td class="px-4 py-3 align-top text-xs text-slate-400">
                                @if($propuesta->fecha_registro)
                                    {{ \Carbon\Carbon::parse($propuesta->fecha_registro)->format('d/m/Y H:i') }}
                                @endif
                            </td>
                            <td class="px-4 py-3 align-top">
                                <div class="flex flex-col items-end gap-1">
                                    <form action="{{ route('admin.propuestas.aprobar', $propuesta->_id) }}" method="POST" onsubmit="return confirm('¿Aprobar este punto de reciclaje?');" class="inline-flex items-center gap-2 text-xs">
                                        @csrf
                                        <button type="submit" class="inline-flex items-center rounded bg-emerald-500/90 px-2 py-1 font-medium text-slate-950 hover:bg-emerald-400">
                                            <i class="fas fa-check mr-1"></i> Aprobar
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.propuestas.rechazar', $propuesta->_id) }}" method="POST" onsubmit="return confirm('¿Rechazar esta propuesta?');" class="inline-flex items-center gap-2 text-xs">
                                        @csrf
                                        <button type="submit" class="inline-flex items-center rounded bg-red-500/90 px-2 py-1 font-medium text-slate-50 hover:bg-red-400">
                                            <i class="fas fa-times mr-1"></i> Rechazar
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-6 text-center text-sm text-slate-500">No hay propuestas pendientes de revisión.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

