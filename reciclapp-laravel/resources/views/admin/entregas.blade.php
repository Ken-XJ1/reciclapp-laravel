@extends('layouts.admin_layout')

@section('title', 'Validar entregas de reciclaje')

@section('content')
    <div class="p-6 space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold tracking-tight">Validar entregas de reciclaje</h1>
                <p class="mt-1 text-sm text-slate-400">Aprueba o rechaza las entregas registradas por los usuarios para otorgar puntos.</p>
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
            <table class="min-w-full text-sm">
                <thead class="bg-slate-900/80">
                    <tr class="text-left text-slate-400">
                        <th class="px-4 py-3">Usuario</th>
                        <th class="px-4 py-3">Correo</th>
                        <th class="px-4 py-3">Tipo de residuo</th>
                        <th class="px-4 py-3">Descripción</th>
                        <th class="px-4 py-3">Cantidad (kg)</th>
                        <th class="px-4 py-3">Puntos sugeridos</th>
                        <th class="px-4 py-3">Fecha registro</th>
                        <th class="px-4 py-3 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($entregas as $entrega)
                        <tr class="border-t border-slate-800/80 hover:bg-slate-900/40">
                            <td class="px-4 py-3 text-slate-100">{{ $entrega->usuario_nombre }}</td>
                            <td class="px-4 py-3 text-slate-400 text-xs">{{ $entrega->usuario_email }}</td>
                            <td class="px-4 py-3">{{ $entrega->tipo_residuo }}</td>
                            <td class="px-4 py-3 text-xs text-slate-300 max-w-xs">{{ $entrega->descripcion_general_materiales }}</td>
                            <td class="px-4 py-3">{{ number_format($entrega->cantidad_kg, 2) }}</td>
                            <td class="px-4 py-3 font-semibold text-emerald-300">{{ $entrega->puntos_sugeridos }}</td>
                            <td class="px-4 py-3 text-xs text-slate-400">
                                @if($entrega->fecha_recoleccion_usuario)
                                    {{ \Carbon\Carbon::parse($entrega->fecha_recoleccion_usuario)->format('d/m/Y') }}
                                @else
                                    {{ optional($entrega->fecha_registro)->format('d/m/Y H:i') ?? '' }}
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-end gap-2">
                                    <form action="{{ route('admin.entregas.gestionar', $entrega->_id) }}" method="POST" class="inline-flex items-center gap-2">
                                        @csrf
                                        <input type="hidden" name="accion" value="aprobar">
                                        <input type="number" name="puntos" min="0" value="{{ (int) ($entrega->puntos_sugeridos ?? 0) }}" class="w-20 rounded-md border border-slate-700 bg-slate-900/80 px-2 py-1 text-xs" title="Puntos a otorgar">
                                        <button type="submit" class="inline-flex items-center rounded-md bg-emerald-500/90 px-3 py-1 text-xs font-medium text-slate-950 hover:bg-emerald-400 transition">
                                            <i class="fas fa-check mr-1"></i> Aprobar
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.entregas.gestionar', $entrega->_id) }}" method="POST" class="inline-flex">
                                        @csrf
                                        <input type="hidden" name="accion" value="rechazar">
                                        <button type="submit" class="inline-flex items-center rounded-md bg-red-500/90 px-3 py-1 text-xs font-medium text-slate-50 hover:bg-red-400 transition">
                                            <i class="fas fa-times mr-1"></i> Rechazar
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-6 text-center text-slate-500 text-sm">
                                No hay entregas pendientes de validación.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
