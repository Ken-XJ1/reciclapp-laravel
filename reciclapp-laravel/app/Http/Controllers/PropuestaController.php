<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PuntoReciclaje;
use App\Models\Usuario;
use App\Models\Auditoria;

class PropuestaController extends Controller
{
    public function index()
    {
        $propuestas = PuntoReciclaje::where('estado', 'pendiente')
            ->orderBy('fecha_registro', 'desc')
            ->get()
            ->map(function ($punto) {
                if (!empty($punto->id_usuario_propone)) {
                    $usuario = Usuario::find($punto->id_usuario_propone);
                    $punto->proponente_nombre = $usuario->nombre ?? null;
                    $punto->proponente_apellido = $usuario->apellido ?? null;
                    $punto->proponente_email = $usuario->email ?? null;
                }

                return $punto;
            });

        return view('admin.propuestas', compact('propuestas'));
    }

    public function aprobar(Request $request, $id)
    {
        $propuesta = PuntoReciclaje::find($id);
        if($propuesta){
            $propuesta->estado = 'aprobada';
            $propuesta->save();

            $admin = auth()->user();
            Auditoria::create([
                'accion'          => 'aprobar_propuesta_punto',
                'usuario'         => $admin->email ?? 'admin',
                'fecha'           => now(),
                'descripcion'     => 'Propuesta de punto aprobada: ' . ($propuesta->nombre ?? '') . ' (ID ' . $propuesta->_id . ')',
                'modulo_afectado' => 'propuestas_admin',
                'resultado'       => 'exitoso',
                'ip'              => $request->ip(),
            ]);
        }
        return redirect()->route('admin.propuestas')->with('success', 'Propuesta aprobada');
    }

    public function rechazar(Request $request, $id)
    {
        $propuesta = PuntoReciclaje::find($id);
        if($propuesta){
            $propuesta->estado = 'rechazada';
            $propuesta->save();

            $admin = auth()->user();
            Auditoria::create([
                'accion'          => 'rechazar_propuesta_punto',
                'usuario'         => $admin->email ?? 'admin',
                'fecha'           => now(),
                'descripcion'     => 'Propuesta de punto rechazada: ' . ($propuesta->nombre ?? '') . ' (ID ' . $propuesta->_id . ')',
                'modulo_afectado' => 'propuestas_admin',
                'resultado'       => 'rechazada',
                'ip'              => $request->ip(),
            ]);
        }
        return redirect()->route('admin.propuestas')->with('success', 'Propuesta rechazada');
    }
}
