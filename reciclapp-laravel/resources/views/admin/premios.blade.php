@extends('layouts.admin_layout')

@section('title', 'Gestión de Premios')

@section('content')
    <div class="p-6 space-y-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-semibold tracking-tight">Gestión de Premios</h1>
                <p class="mt-1 text-sm text-slate-400">Administra el catálogo de premios y los canjes realizados por los usuarios.</p>
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

        {{-- Premios --}}
        <div class="grid gap-6 lg:grid-cols-3">
            {{-- Lista de premios --}}
            <div class="lg:col-span-2 bg-slate-900/60 border border-slate-800 rounded-xl overflow-hidden">
                <div class="flex items-center justify-between px-4 py-3 border-b border-slate-800">
                    <h2 class="text-sm font-semibold text-slate-200">Premios configurados</h2>
                </div>

                <table class="min-w-full text-sm">
                    <thead class="bg-slate-900/80">
                        <tr class="text-left text-slate-400">
                            <th class="px-4 py-3">Premio</th>
                            <th class="px-4 py-3">Puntos</th>
                            <th class="px-4 py-3">Stock</th>
                            <th class="px-4 py-3">Estado</th>
                            <th class="px-4 py-3">Imagen</th>
                            <th class="px-4 py-3 text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($premios as $premio)
                            <tr class="border-t border-slate-800/80 hover:bg-slate-900/40">
                                <td class="px-4 py-3 align-top">
                                    <div class="font-medium text-slate-100">{{ $premio->nombre }}</div>
                                    <div class="text-xs text-slate-400 max-w-xs line-clamp-2">{{ $premio->descripcion }}</div>
                                </td>
                                <td class="px-4 py-3 align-top">{{ $premio->puntos_necesarios }}</td>
                                <td class="px-4 py-3 align-top">
                                    @if($premio->stock === null)
                                        <span class="text-xs text-slate-400">Ilimitado</span>
                                    @else
                                        {{ $premio->stock }}
                                    @endif
                                </td>
                                <td class="px-4 py-3 align-top">
                                    @if($premio->estado === 'activo')
                                        <span class="inline-flex items-center rounded-full bg-emerald-500/20 px-2 py-0.5 text-[11px] font-medium text-emerald-300">Activo</span>
                                    @else
                                        <span class="inline-flex items-center rounded-full bg-slate-700/60 px-2 py-0.5 text-[11px] font-medium text-slate-300">Inactivo</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 align-top">
                                    @if($premio->imagen)
                                        <img src="{{ asset($premio->imagen) }}" alt="{{ $premio->nombre }}" class="h-10 w-10 rounded object-cover">
                                    @else
                                        <span class="text-xs text-slate-500">Sin imagen</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 align-top">
                                    <div class="flex items-center justify-end gap-2">
                                        {{-- Edición rápida --}}
                                        <form action="{{ route('admin.premio.update', $premio->_id) }}" method="POST" class="space-y-1 text-xs bg-slate-950/60 border border-slate-800 rounded-md px-2 py-2 w-52 hidden md:block">
                                            @csrf
                                            @method('PUT')
                                            <input type="text" name="nombre" value="{{ $premio->nombre }}" class="w-full mb-1 rounded bg-slate-900/80 border border-slate-700 px-2 py-1" placeholder="Nombre">
                                            <input type="number" name="puntos_necesarios" value="{{ $premio->puntos_necesarios }}" min="1" class="w-full mb-1 rounded bg-slate-900/80 border border-slate-700 px-2 py-1" placeholder="Puntos">
                                            <input type="number" name="stock" value="{{ $premio->stock }}" min="0" class="w-full mb-1 rounded bg-slate-900/80 border border-slate-700 px-2 py-1" placeholder="Stock (vacío = ilimitado)">
                                            <input type="text" name="imagen" value="{{ $premio->imagen }}" class="w-full mb-1 rounded bg-slate-900/80 border border-slate-700 px-2 py-1" placeholder="Ruta imagen (opcional)">
                                            <select name="estado" class="w-full rounded bg-slate-900/80 border border-slate-700 px-2 py-1">
                                                <option value="activo" {{ $premio->estado === 'activo' ? 'selected' : '' }}>Activo</option>
                                                <option value="inactivo" {{ $premio->estado === 'inactivo' ? 'selected' : '' }}>Inactivo</option>
                                            </select>
                                            <button type="submit" class="mt-1 inline-flex w-full items-center justify-center rounded bg-emerald-500/90 px-2 py-1 font-semibold text-slate-950 hover:bg-emerald-400">Guardar</button>
                                        </form>

                                        {{-- Eliminar --}}
                                        <form action="{{ route('admin.premio.destroy', $premio->_id) }}" method="POST" onsubmit="return confirm('¿Seguro que deseas eliminar este premio?');">
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
                                <td colspan="6" class="px-4 py-6 text-center text-sm text-slate-500">No hay premios configurados.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Crear nuevo premio --}}
            <div class="bg-slate-900/60 border border-slate-800 rounded-xl p-4 space-y-3">
                <h2 class="text-sm font-semibold text-slate-200 flex items-center gap-2">
                    <i class="fas fa-gift text-emerald-400"></i>
                    Nuevo premio
                </h2>

                <form action="{{ route('admin.premio.store') }}" method="POST" class="space-y-3 text-sm">
                    @csrf
                    <div>
                        <label class="block text-xs font-medium text-slate-400 mb-1">Nombre</label>
                        <input type="text" name="nombre" class="w-full rounded border border-slate-700 bg-slate-950/60 px-3 py-2 text-sm" required>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-slate-400 mb-1">Descripción</label>
                        <textarea name="descripcion" rows="3" class="w-full rounded border border-slate-700 bg-slate-950/60 px-3 py-2 text-sm"></textarea>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-medium text-slate-400 mb-1">Puntos necesarios</label>
                            <input type="number" name="puntos_necesarios" min="1" class="w-full rounded border border-slate-700 bg-slate-950/60 px-3 py-2 text-sm" required>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-400 mb-1">Stock (opcional)</label>
                            <input type="number" name="stock" min="0" class="w-full rounded border border-slate-700 bg-slate-950/60 px-3 py-2 text-sm" placeholder="Vacío = ilimitado">
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-slate-400 mb-1">URL / ruta de imagen</label>
                        <input type="text" name="imagen" class="w-full rounded border border-slate-700 bg-slate-950/60 px-3 py-2 text-sm" placeholder="img/premios/ejemplo.png">
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-slate-400 mb-1">Estado</label>
                        <select name="estado" class="w-full rounded border border-slate-700 bg-slate-950/60 px-3 py-2 text-sm">
                            <option value="activo">Activo</option>
                            <option value="inactivo">Inactivo</option>
                        </select>
                    </div>

                    <button type="submit" class="inline-flex w-full items-center justify-center rounded-md bg-emerald-500/90 px-3 py-2 text-sm font-semibold text-slate-950 hover:bg-emerald-400">
                        <i class="fas fa-save mr-2"></i>Guardar premio
                    </button>
                </form>
            </div>
        </div>

        {{-- Canjes --}}
        <div class="bg-slate-900/60 border border-slate-800 rounded-xl overflow-hidden">
            <div class="flex items-center justify-between px-4 py-3 border-b border-slate-800">
                <h2 class="text-sm font-semibold text-slate-200">Canjes de premios</h2>
            </div>

            <table class="min-w-full text-sm">
                <thead class="bg-slate-900/80">
                    <tr class="text-left text-slate-400">
                        <th class="px-4 py-3">Usuario</th>
                        <th class="px-4 py-3">Premio</th>
                        <th class="px-4 py-3">Puntos usados</th>
                        <th class="px-4 py-3">Código</th>
                        <th class="px-4 py-3">Fecha canje</th>
                        <th class="px-4 py-3">Estado</th>
                        <th class="px-4 py-3">Observaciones</th>
                        <th class="px-4 py-3 text-right">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($canjes as $canje)
                        <tr class="border-t border-slate-800/80 hover:bg-slate-900/40">
                            <td class="px-4 py-3 align-top">
                                <div class="text-slate-100 text-sm">{{ $canje->usuario_nombre }}</div>
                                <div class="text-[11px] text-slate-400">{{ $canje->usuario_email }}</div>
                            </td>
                            <td class="px-4 py-3 align-top">{{ $canje->premio_nombre }}</td>
                            <td class="px-4 py-3 align-top font-semibold text-emerald-300">{{ $canje->puntos_usados }}</td>
                            <td class="px-4 py-3 align-top text-xs font-mono">{{ $canje->codigo_canje_fisico }}</td>
                            <td class="px-4 py-3 align-top text-xs text-slate-400">
                                @if($canje->fecha_canje)
                                    {{ \Carbon\Carbon::parse($canje->fecha_canje)->format('d/m/Y H:i') }}
                                @endif
                            </td>
                            <td class="px-4 py-3 align-top">
                                @php
                                    $estado = $canje->estado_canje;
                                @endphp
                                @if($estado === 'pendiente_entrega')
                                    <span class="inline-flex rounded-full bg-amber-500/20 px-2 py-0.5 text-[11px] font-medium text-amber-300">Pendiente</span>
                                @elseif($estado === 'entregado')
                                    <span class="inline-flex rounded-full bg-emerald-500/20 px-2 py-0.5 text-[11px] font-medium text-emerald-300">Entregado</span>
                                @elseif($estado === 'cancelado')
                                    <span class="inline-flex rounded-full bg-red-500/20 px-2 py-0.5 text-[11px] font-medium text-red-300">Cancelado</span>
                                @else
                                    <span class="inline-flex rounded-full bg-slate-700/40 px-2 py-0.5 text-[11px] font-medium text-slate-300">{{ $estado }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 align-top text-xs text-slate-300 max-w-xs">
                                {{ $canje->observaciones_entrega ?? 'Sin observaciones' }}
                            </td>
                            <td class="px-4 py-3 align-top">
                                @if($canje->estado_canje === 'pendiente_entrega')
                                    <div class="flex flex-col items-end gap-1">
                                        <form action="{{ route('admin.canje.gestionar', $canje->_id) }}" method="POST" class="inline-flex items-center gap-2 text-xs">
                                            @csrf
                                            <input type="hidden" name="accion" value="entregado">
                                            <input type="text" name="observaciones" placeholder="Obs. entrega" class="w-32 rounded border border-slate-700 bg-slate-950/60 px-2 py-1">
                                            <button type="submit" class="inline-flex items-center rounded bg-emerald-500/90 px-2 py-1 font-medium text-slate-950 hover:bg-emerald-400">
                                                <i class="fas fa-check mr-1"></i> Entregado
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.canje.gestionar', $canje->_id) }}" method="POST" class="inline-flex items-center gap-2 text-xs">
                                            @csrf
                                            <input type="hidden" name="accion" value="cancelado">
                                            <input type="text" name="observaciones" placeholder="Motivo cancelación" class="w-32 rounded border border-slate-700 bg-slate-950/60 px-2 py-1">
                                            <button type="submit" class="inline-flex items-center rounded bg-red-500/90 px-2 py-1 font-medium text-slate-50 hover:bg-red-400">
                                                <i class="fas fa-times mr-1"></i> Cancelar
                                            </button>
                                        </form>
                                    </div>
                                @else
                                    <span class="text-[11px] text-slate-500">Gestionado</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-6 text-center text-sm text-slate-500">No hay canjes registrados.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection

