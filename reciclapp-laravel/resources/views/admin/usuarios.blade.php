@extends('layouts.admin_layout')

@section('title', 'Gestión de Usuarios')

@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-xl font-semibold tracking-tight flex items-center gap-2">
            <i class="fas fa-users"></i>
            <span>Gestión de usuarios</span>
        </h1>
        <p class="text-sm text-slate-400 mt-1">Administra roles y estado de los usuarios registrados en ReciclApp.</p>
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
                <th class="px-4 py-3 text-left text-xs font-medium text-slate-400">Nombre</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-slate-400">Email</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-slate-400">Rol</th>
                <th class="px-4 py-3 text-left text-xs font-medium text-slate-400">Estado</th>
                <th class="px-4 py-3 text-right text-xs font-medium text-slate-400">Acciones</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-800">
            @forelse($usuarios as $usuario)
                <tr class="hover:bg-slate-900/60">
                    <td class="px-4 py-3 align-top">
                        <div class="font-medium text-slate-50">{{ $usuario->nombre }} {{ $usuario->apellido }}</div>
                        <div class="text-xs text-slate-500">ID: {{ $usuario->id_usuario }}</div>
                    </td>
                    <td class="px-4 py-3 align-top text-slate-200">{{ $usuario->email }}</td>
                    <td class="px-4 py-3 align-top">
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-[11px] font-medium
                            {{ str_contains(strtolower($usuario->rol ?? ''), 'admin') ? 'bg-amber-500/10 text-amber-200 border border-amber-500/40' : 'bg-emerald-500/10 text-emerald-200 border border-emerald-500/40' }}">
                            {{ ucfirst($usuario->rol ?? 'usuario') }}
                        </span>
                    </td>
                    <td class="px-4 py-3 align-top">
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-[11px] font-medium
                            {{ ($usuario->estado ?? '') === 'activo' ? 'bg-emerald-500/10 text-emerald-200 border border-emerald-500/40' : 'bg-slate-500/10 text-slate-200 border border-slate-600/40' }}">
                            {{ ucfirst($usuario->estado ?? 'activo') }}
                        </span>
                    </td>
                    <td class="px-4 py-3 align-top">
                        <div class="flex items-center justify-end gap-2 text-xs">
                            <form action="{{ route('admin.usuario.rol', $usuario->_id) }}" method="POST" class="inline-flex items-center gap-1">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="rol" value="{{ str_contains(strtolower($usuario->rol ?? ''), 'admin') ? 'usuario' : 'administrador' }}">
                                <button type="submit" class="px-3 py-1 rounded-lg bg-slate-800 hover:bg-slate-700 border border-slate-700">
                                    {{ str_contains(strtolower($usuario->rol ?? ''), 'admin') ? 'Pasar a usuario' : 'Hacer admin' }}
                                </button>
                            </form>

                            <form action="{{ route('admin.usuario.eliminar', $usuario->_id) }}" method="POST" onsubmit="return confirm('¿Seguro que deseas eliminar este usuario?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-3 py-1 rounded-lg bg-rose-600/80 hover:bg-rose-500 text-slate-50">
                                    Eliminar
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-4 py-6 text-center text-sm text-slate-500">No hay usuarios registrados.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $usuarios->links() }}
    </div>
@endsection
