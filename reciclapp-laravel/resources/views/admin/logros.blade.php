@extends('layouts.admin_layout')

@section('title', 'Gestión de Logros')

@section('content')
    <div class="p-6 space-y-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold tracking-tight">Logros</h1>
                <p class="mt-1 text-sm text-slate-400">Define logros que los usuarios pueden desbloquear según sus puntos acumulados.</p>
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
            {{-- Lista de logros --}}
            <div class="lg:col-span-2 bg-slate-900/60 border border-slate-800 rounded-xl overflow-hidden">
                <div class="flex items-center justify-between px-4 py-3 border-b border-slate-800">
                    <h2 class="text-sm font-semibold text-slate-200">Logros configurados</h2>
                </div>

                <table class="min-w-full text-sm">
                    <thead class="bg-slate-900/80">
                        <tr class="text-left text-slate-400">
                            <th class="px-4 py-3">Nombre</th>
                            <th class="px-4 py-3">Puntos requeridos</th>
                            <th class="px-4 py-3">Icono</th>
                            <th class="px-4 py-3 text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($logros as $logro)
                            <tr class="border-t border-slate-800/80 hover:bg-slate-900/40">
                                <td class="px-4 py-3 align-top">
                                    <div class="font-medium text-slate-100 flex items-center gap-2">
                                        @if($logro->icono)
                                            <span class="inline-flex h-6 w-6 items-center justify-center rounded-full bg-emerald-500/20 text-emerald-300 text-xs">
                                                <i class="{{ $logro->icono }}"></i>
                                            </span>
                                        @endif
                                        <span>{{ $logro->nombre }}</span>
                                    </div>
                                    <div class="text-xs text-slate-400 max-w-xs line-clamp-2">{{ $logro->descripcion }}</div>
                                </td>
                                <td class="px-4 py-3 align-top text-emerald-300 font-semibold">{{ $logro->puntos_requeridos }}</td>
                                <td class="px-4 py-3 align-top text-xs text-slate-300">
                                    {{ $logro->icono ?? 'N/A' }}
                                </td>
                                <td class="px-4 py-3 align-top">
                                    <div class="flex flex-col items-end gap-2">
                                        {{-- Edición rápida --}}
                                        <form action="{{ route('admin.logros.update', $logro->_id) }}" method="POST" class="space-y-1 text-xs bg-slate-950/60 border border-slate-800 rounded-md px-2 py-2 w-64 hidden md:block">
                                            @csrf
                                            @method('PUT')
                                            <input type="text" name="nombre" value="{{ $logro->nombre }}" class="w-full mb-1 rounded bg-slate-900/80 border border-slate-700 px-2 py-1" placeholder="Nombre">
                                            <textarea name="descripcion" class="w-full mb-1 rounded bg-slate-900/80 border border-slate-700 px-2 py-1" rows="2" placeholder="Descripción">{{ $logro->descripcion }}</textarea>
                                            <input type="number" name="puntos_requeridos" value="{{ $logro->puntos_requeridos }}" min="1" class="w-full mb-1 rounded bg-slate-900/80 border border-slate-700 px-2 py-1" placeholder="Puntos requeridos">
                                            <input type="text" name="icono" value="{{ $logro->icono }}" class="w-full mb-1 rounded bg-slate-900/80 border border-slate-700 px-2 py-1" placeholder="Clase de icono (FontAwesome)">
                                            <button type="submit" class="mt-1 inline-flex w-full items-center justify-center rounded bg-emerald-500/90 px-2 py-1 font-semibold text-slate-950 hover:bg-emerald-400">Guardar</button>
                                        </form>

                                        {{-- Eliminar --}}
                                        <form action="{{ route('admin.logros.destroy', $logro->_id) }}" method="POST" onsubmit="return confirm('¿Eliminar este logro?');" class="inline-flex">
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
                                <td colspan="4" class="px-4 py-6 text-center text-sm text-slate-500">No hay logros configurados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Crear nuevo logro --}}
            <div class="bg-slate-900/60 border border-slate-800 rounded-xl p-4 space-y-3">
                <h2 class="text-sm font-semibold text-slate-200 flex items-center gap-2">
                    <i class="fas fa-trophy text-emerald-400"></i>
                    Nuevo logro
                </h2>

                <form action="{{ route('admin.logros.store') }}" method="POST" class="space-y-3 text-sm">
                    @csrf
                    <div>
                        <label class="block text-xs font-medium text-slate-400 mb-1">Nombre</label>
                        <input type="text" name="nombre" class="w-full rounded border border-slate-700 bg-slate-950/60 px-3 py-2 text-sm" required>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-slate-400 mb-1">Descripción</label>
                        <textarea name="descripcion" rows="3" class="w-full rounded border border-slate-700 bg-slate-950/60 px-3 py-2 text-sm"></textarea>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-slate-400 mb-1">Puntos requeridos</label>
                        <input type="number" name="puntos_requeridos" min="1" class="w-full rounded border border-slate-700 bg-slate-950/60 px-3 py-2 text-sm" required>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-slate-400 mb-1">Icono (clase FontAwesome, opcional)</label>
                        <input type="text" name="icono" class="w-full rounded border border-slate-700 bg-slate-950/60 px-3 py-2 text-sm" placeholder="Ej: fas fa-medal">
                    </div>

                    <button type="submit" class="inline-flex w-full items-center justify-center rounded-md bg-emerald-500/90 px-3 py-2 text-sm font-semibold text-slate-950 hover:bg-emerald-400">
                        <i class="fas fa-save mr-2"></i>Guardar logro
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection

