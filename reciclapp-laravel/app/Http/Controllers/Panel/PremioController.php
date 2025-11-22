<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Premio;
use App\Models\CanjeUsuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Models\Auditoria;

class PremioController extends Controller
{
    public function index()
    {
        $premios = Premio::all();
        return view('panel.premios', compact('premios'));
    }

    public function canjear(Request $request, $id)
    {
        $premio = Premio::find($id);

        if (!$premio) {
            return back()->with('error', 'El premio no existe.');
        }

        $usuarioId = Auth::id();

        if (!$usuarioId) {
            return redirect()->route('login');
        }

        $usuario = Auth::user();

        $puntosNecesarios = (int) ($premio->puntos_necesarios ?? 0);
        $stockDisponible = $premio->stock ?? null;

        if ($puntosNecesarios <= 0) {
            return back()->with('error', 'Este premio no tiene puntos configurados.');
        }

        if ((int) ($usuario->puntos_acumulados ?? 0) < $puntosNecesarios) {
            return back()->with('error', 'Puntos insuficientes');
        }

        if ($stockDisponible !== null && $stockDisponible <= 0) {
            return back()->with('error', 'No hay stock disponible para este premio.');
        }

        $codigoCanje = 'CANJE_' . strtoupper(Str::random(8));

        CanjeUsuario::create([
            'id_usuario'         => (string) $usuarioId,
            'id_premio'          => $premio->_id,
            'fecha_canje'        => now(),
            'puntos_usados'      => $puntosNecesarios,
            'codigo_canje_fisico'=> $codigoCanje,
            'estado_canje'       => 'pendiente_entrega',
        ]);

        // Descontar puntos del usuario
        $usuario->puntos_acumulados = (int) ($usuario->puntos_acumulados ?? 0) - $puntosNecesarios;
        $usuario->save();

        // Descontar stock si aplica
        if ($stockDisponible !== null) {
            $premio->stock = max(0, (int) $stockDisponible - 1);
            $premio->save();
        }

        // Auditoría: usuario canjea un premio
        Auditoria::create([
            'accion'          => 'canje_premio',
            'usuario'         => $usuario->email ?? 'desconocido',
            'fecha'           => now(),
            'descripcion'     => 'Canje del premio ' . ($premio->nombre ?? '') . ' por ' . $puntosNecesarios . ' puntos. Código: ' . $codigoCanje,
            'modulo_afectado' => 'premios_panel_usuario',
            'resultado'       => 'exitoso',
            'ip'              => $request->ip(),
        ]);

        return back()->with('success', 'Canje registrado correctamente. ¡Espera la confirmación y guarda tu código: ' . $codigoCanje . '!');
    }
}
