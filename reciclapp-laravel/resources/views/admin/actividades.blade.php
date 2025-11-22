@extends('layouts.admin_layout')

@section('title', 'Actividades y eventos')

@section('content')
    <div class="p-6 space-y-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold tracking-tight">Actividades y eventos</h1>
                <p class="mt-1 text-sm text-slate-400">Crea y gestiona actividades para que los usuarios las vean en su panel.</p>
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

        <div class="grid gap-6 lg:grid-cols-3">
            {{-- Lista de actividades --}}
            <div class="lg:col-span-2 bg-slate-900/60 border border-slate-800 rounded-xl overflow-hidden">
                <div class="flex items-center justify-between px-4 py-3 border-b border-slate-800">
                    <h2 class="text-sm font-semibold text-slate-200">Actividades registradas</h2>
                </div>

                <table class="min-w-full text-sm">
                    <thead class="bg-slate-900/80">
                        <tr class="text-left text-slate-400">
                            <th class="px-4 py-3">Título</th>
                            <th class="px-4 py-3">Fecha</th>
                            <th class="px-4 py-3">Lugar</th>
                            <th class="px-4 py-3">Estado</th>
                            <th class="px-4 py-3 text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($actividades as $actividad)
                            <tr class="border-t border-slate-800/80 hover:bg-slate-900/40">
                                <td class="px-4 py-3 align-top">
                                    <div class="font-medium text-slate-100">{{ $actividad->titulo }}</div>
                                    <div class="text-xs text-slate-400 max-w-xs line-clamp-2">{{ $actividad->descripcion }}</div>
                                </td>
                                <td class="px-4 py-3 align-top text-xs text-slate-300">
                                    @if($actividad->fecha)
                                        {{ \Carbon\Carbon::parse($actividad->fecha)->format('d/m/Y H:i') }}
                                    @endif
                                </td>
                                <td class="px-4 py-3 align-top text-slate-300">{{ $actividad->lugar ?? 'N/A' }}</td>
                                <td class="px-4 py-3 align-top">
                                    @php $estado = $actividad->estado; @endphp
                                    @if($estado === 'activa')
                                        <span class="inline-flex rounded-full bg-emerald-500/20 px-2 py-0.5 text-[11px] font-medium text-emerald-300">Activa</span>
                                    @elseif($estado === 'finalizada')
                                        <span class="inline-flex rounded-full bg-slate-600/40 px-2 py-0.5 text-[11px] font-medium text-slate-200">Finalizada</span>
                                    @elseif($estado === 'cancelada')
                                        <span class="inline-flex rounded-full bg-red-500/20 px-2 py-0.5 text-[11px] font-medium text-red-300">Cancelada</span>
                                    @else
                                        <span class="inline-flex rounded-full bg-slate-700/40 px-2 py-0.5 text-[11px] font-medium text-slate-300">{{ $estado }}</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 align-top">
                                    <div class="flex flex-col items-end gap-2">
                                        {{-- Formulario de edición rápida --}}
                                        <form action="{{ route('admin.actividades.update', $actividad->_id) }}" method="POST" class="space-y-1 text-xs bg-slate-950/60 border border-slate-800 rounded-md px-2 py-2 w-60 hidden md:block">
                                            @csrf
                                            @method('PUT')
                                            <input type="text" name="titulo" value="{{ $actividad->titulo }}" class="w-full mb-1 rounded bg-slate-900/80 border border-slate-700 px-2 py-1" placeholder="Título">
                                            <textarea name="descripcion" class="w-full mb-1 rounded bg-slate-900/80 border border-slate-700 px-2 py-1" rows="2" placeholder="Descripción">{{ $actividad->descripcion }}</textarea>
                                            <input type="datetime-local" name="fecha" value="{{ $actividad->fecha ? \Carbon\Carbon::parse($actividad->fecha)->format('Y-m-d\TH:i') : '' }}" class="w-full mb-1 rounded bg-slate-900/80 border border-slate-700 px-2 py-1">
                                            <input type="text" name="lugar" value="{{ $actividad->lugar }}" class="w-full mb-1 rounded bg-slate-900/80 border border-slate-700 px-2 py-1" placeholder="Lugar">
                                            <select name="estado" class="w-full rounded bg-slate-900/80 border border-slate-700 px-2 py-1">
                                                <option value="activa" {{ $actividad->estado === 'activa' ? 'selected' : '' }}>Activa</option>
                                                <option value="finalizada" {{ $actividad->estado === 'finalizada' ? 'selected' : '' }}>Finalizada</option>
                                                <option value="cancelada" {{ $actividad->estado === 'cancelada' ? 'selected' : '' }}>Cancelada</option>
                                            </select>
                                            <button type="submit" class="mt-1 inline-flex w-full items-center justify-center rounded bg-emerald-500/90 px-2 py-1 font-semibold text-slate-950 hover:bg-emerald-400">Guardar</button>
                                        </form>

                                        {{-- Eliminar --}}
                                        <form action="{{ route('admin.actividades.destroy', $actividad->_id) }}" method="POST" onsubmit="return confirm('¿Eliminar esta actividad?');" class="inline-flex">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex items-center rounded-md bg-red-500/80 px-2 py-1 text-xs font-medium text-slate-50 hover:bg-red-400">
                                                <i class="fas fa-trash mr-1"></i> Eliminar
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-6 text-center text-sm text-slate-500">No hay actividades registradas.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Crear nueva actividad --}}
            <div class="bg-slate-900/60 border border-slate-800 rounded-xl p-4 space-y-3">
                <h2 class="text-sm font-semibold text-slate-200 flex items-center gap-2">
                    <i class="fas fa-calendar-plus text-emerald-400"></i>
                    Nueva actividad
                </h2>

                <form action="{{ route('admin.actividades.store') }}" method="POST" class="space-y-3 text-sm">
                    @csrf
                    <div>
                        <label class="block text-xs font-medium text-slate-400 mb-1">Título</label>
                        <input type="text" name="titulo" class="w-full rounded border border-slate-700 bg-slate-950/60 px-3 py-2 text-sm" required>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-slate-400 mb-1">Descripción</label>
                        <textarea name="descripcion" rows="4" class="w-full rounded border border-slate-700 bg-slate-950/60 px-3 py-2 text-sm"></textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-slate-400 mb-1">Fecha y hora</label>
                        <input type="datetime-local" name="fecha" class="w-full rounded border border-slate-700 bg-slate-950/60 px-3 py-2 text-sm" required>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-slate-400 mb-1">Lugar</label>
                        <input type="text" name="lugar" class="w-full rounded border border-slate-700 bg-slate-950/60 px-3 py-2 text-sm" placeholder="Ej: Parque central, Salón comunal...">
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-slate-400 mb-1">Estado</label>
                        <select name="estado" class="w-full rounded border border-slate-700 bg-slate-950/60 px-3 py-2 text-sm">
                            <option value="activa">Activa</option>
                            <option value="finalizada">Finalizada</option>
                            <option value="cancelada">Cancelada</option>
                        </select>
                    </div>

                    <button type="submit" class="inline-flex w-full items-center justify-center rounded-md bg-emerald-500/90 px-3 py-2 text-sm font-semibold text-slate-950 hover:bg-emerald-400">
                        <i class="fas fa-save mr-2"></i>Guardar actividad
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection

