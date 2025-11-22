<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Premio;
use App\Models\CanjeUsuario;
use App\Models\Usuario;
use App\Models\Auditoria;

class PremioController extends Controller
{
    public function index()
    {
        $premios = Premio::orderBy('nombre')->get();

        $canjes = CanjeUsuario::orderBy('fecha_canje', 'desc')->get()->map(function ($canje) {
            $usuario = Usuario::find($canje->id_usuario);
            $premio = Premio::find($canje->id_premio);

            $canje->usuario_nombre = $usuario->nombre ?? 'Usuario';
            $canje->usuario_email = $usuario->email ?? '';
            $canje->premio_nombre = $premio->nombre ?? '';

            return $canje;
        });

        return view('admin.premios', compact('premios', 'canjes'));
    }

    public function show($id)
    {
        $premio = Premio::find($id);
        return view('admin.premio_detalle', compact('premio'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre'            => 'required|string|max:255',
            'descripcion'       => 'nullable|string|max:1000',
            'puntos_necesarios' => 'required|integer|min:1',
            'stock'             => 'nullable|integer|min:0',
            'estado'            => 'required|string|in:activo,inactivo',
            'imagen'            => 'nullable|string|max:500', // URL o ruta
        ]);

        $premio = Premio::create($data);

        // Auditoría: admin crea premio
        $admin = auth()->user();
        Auditoria::create([
            'accion'          => 'crear_premio',
            'usuario'         => $admin->email ?? 'admin',
            'fecha'           => now(),
            'descripcion'     => 'Premio creado: ' . ($premio->nombre ?? ''),
            'modulo_afectado' => 'premios_admin',
            'resultado'       => 'exitoso',
            'ip'              => $request->ip(),
        ]);

        return redirect()->route('admin.premios')->with('success', 'Premio creado correctamente');
    }

    public function update(Request $request, $id)
    {
        $premio = Premio::find($id);
        if($premio){
            $data = $request->validate([
                'nombre'            => 'required|string|max:255',
                'descripcion'       => 'nullable|string|max:1000',
                'puntos_necesarios' => 'required|integer|min:1',
                'stock'             => 'nullable|integer|min:0',
                'estado'            => 'required|string|in:activo,inactivo',
                'imagen'            => 'nullable|string|max:500',
            ]);

            $premio->update($data);

            // Auditoría: admin actualiza premio
            $admin = auth()->user();
            Auditoria::create([
                'accion'          => 'actualizar_premio',
                'usuario'         => $admin->email ?? 'admin',
                'fecha'           => now(),
                'descripcion'     => 'Premio actualizado: ' . ($premio->nombre ?? '') . ' (ID ' . $premio->_id . ')',
                'modulo_afectado' => 'premios_admin',
                'resultado'       => 'exitoso',
                'ip'              => $request->ip(),
            ]);
        }
        return redirect()->route('admin.premios')->with('success', 'Premio actualizado correctamente');
    }

    public function destroy($id)
    {
        $premio = Premio::find($id);
        if($premio){
            $nombre = $premio->nombre;
            $premio->delete();

            // Auditoría: admin elimina premio
            $admin = auth()->user();
            Auditoria::create([
                'accion'          => 'eliminar_premio',
                'usuario'         => $admin->email ?? 'admin',
                'fecha'           => now(),
                'descripcion'     => 'Premio eliminado: ' . ($nombre ?? '') . ' (ID ' . $premio->_id . ')',
                'modulo_afectado' => 'premios_admin',
                'resultado'       => 'exitoso',
                'ip'              => request()->ip(),
            ]);
        }
        return redirect()->route('admin.premios')->with('success', 'Premio eliminado correctamente');
    }

    /**
     * Gestionar canjes de premios desde el admin (entregado / cancelado).
     */
    public function gestionarCanje(Request $request, $id)
    {
        $data = $request->validate([
            'accion'       => 'required|in:entregado,cancelado',
            'observaciones'=> 'nullable|string|max:500',
        ]);

        $canje = CanjeUsuario::find($id);

        if (!$canje || $canje->estado_canje !== 'pendiente_entrega') {
            return back()->with('error', 'El canje no existe o ya fue gestionado.');
        }

        $premio = Premio::find($canje->id_premio);
        $usuario = Usuario::find($canje->id_usuario);

        if ($data['accion'] === 'entregado') {
            $canje->estado_canje = 'entregado';
            $canje->fecha_entrega = now();
            $canje->observaciones_entrega = $data['observaciones'] ?? null;
            $canje->save();

            // Auditoría: admin marca canje como entregado
            $admin = auth()->user();
            Auditoria::create([
                'accion'          => 'entregar_canje_premio',
                'usuario'         => $admin->email ?? 'admin',
                'fecha'           => now(),
                'descripcion'     => 'Canje entregado: premio ' . ($premio->nombre ?? '') . ' para usuario ' . $canje->id_usuario . '.',
                'modulo_afectado' => 'premios_admin',
                'resultado'       => 'exitoso',
                'ip'              => $request->ip(),
            ]);

            return back()->with('success', 'Canje marcado como entregado.');
        }

        // cancelado: devolver puntos y stock
        if ($usuario && $canje->puntos_usados) {
            $usuario->puntos_acumulados = (int) ($usuario->puntos_acumulados ?? 0) + (int) $canje->puntos_usados;
            $usuario->save();
        }

        if ($premio && $premio->stock !== null) {
            $premio->stock = (int) $premio->stock + 1;
            $premio->save();
        }

        $canje->estado_canje = 'cancelado';
        $canje->fecha_entrega = now();
        $canje->observaciones_entrega = $data['observaciones'] ?? null;
        $canje->save();

        // Auditoría: admin cancela canje
        $admin = auth()->user();
        Auditoria::create([
            'accion'          => 'cancelar_canje_premio',
            'usuario'         => $admin->email ?? 'admin',
            'fecha'           => now(),
            'descripcion'     => 'Canje cancelado: premio ' . ($premio->nombre ?? '') . ' para usuario ' . $canje->id_usuario . ', puntos devueltos: ' . ($canje->puntos_usados ?? 0) . '.',
            'modulo_afectado' => 'premios_admin',
            'resultado'       => 'exitoso',
            'ip'              => $request->ip(),
        ]);

        return back()->with('success', 'Canje cancelado y puntos devueltos al usuario.');
    }
}
