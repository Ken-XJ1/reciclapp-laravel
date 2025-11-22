<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EntregaReciclaje;
use App\Models\Usuario;
use App\Models\Auditoria;

class EntregaReciclajeAdminController extends Controller
{
    public function index()
    {
        $entregas = EntregaReciclaje::where('estado', 'pendiente')
            ->orderBy('fecha_registro', 'desc')
            ->get()
            ->map(function ($entrega) {
                $usuario = Usuario::find($entrega->id_usuario);
                $entrega->usuario_nombre = $usuario->nombre ?? 'Usuario';
                $entrega->usuario_email = $usuario->email ?? '';
                return $entrega;
            });

        return view('admin.entregas', compact('entregas'));
    }

    public function gestionar(Request $request, $id)
    {
        $data = $request->validate([
            'accion' => 'required|in:aprobar,rechazar',
            'puntos' => 'nullable|integer|min:0',
            'observaciones' => 'nullable|string|max:500',
        ]);

        $entrega = EntregaReciclaje::find($id);

        if (!$entrega || $entrega->estado !== 'pendiente') {
            return back()->with('error', 'La entrega no existe o ya fue gestionada.');
        }

        if ($data['accion'] === 'aprobar') {
            $puntos = $data['puntos'] ?? (int) ($entrega->puntos_sugeridos ?? 0);
            $entrega->estado = 'aprobada';
            $entrega->puntos_otorgados = $puntos;
            $entrega->fecha_validacion = now();
            $entrega->observaciones_admin = $data['observaciones'] ?? null;
            $entrega->save();

            $usuario = Usuario::find($entrega->id_usuario);
            if ($usuario) {
                $actual = (int) ($usuario->puntos_acumulados ?? 0);
                $usuario->puntos_acumulados = $actual + $puntos;
                $usuario->save();
            }

            // Auditoría: admin aprueba entrega de reciclaje
            $admin = auth()->user();
            Auditoria::create([
                'accion'          => 'aprobar_entrega_reciclaje',
                'usuario'         => $admin->email ?? 'admin',
                'fecha'           => now(),
                'descripcion'     => 'Entrega ID ' . $entrega->_id . ' aprobada para usuario ' . $entrega->id_usuario . ' con ' . $puntos . ' puntos.',
                'modulo_afectado' => 'entregas_admin',
                'resultado'       => 'exitoso',
                'ip'              => $request->ip(),
            ]);

            return back()->with('success', 'Entrega aprobada y puntos asignados al usuario.');
        }

        // Rechazar
        $entrega->estado = 'rechazada';
        $entrega->fecha_validacion = now();
        $entrega->observaciones_admin = $data['observaciones'] ?? null;
        $entrega->save();

        // Auditoría: admin rechaza entrega de reciclaje
        $admin = auth()->user();
        Auditoria::create([
            'accion'          => 'rechazar_entrega_reciclaje',
            'usuario'         => $admin->email ?? 'admin',
            'fecha'           => now(),
            'descripcion'     => 'Entrega ID ' . $entrega->_id . ' rechazada para usuario ' . $entrega->id_usuario . '.',
            'modulo_afectado' => 'entregas_admin',
            'resultado'       => 'rechazada',
            'ip'              => $request->ip(),
        ]);

        return back()->with('success', 'Entrega rechazada.');
    }
}
