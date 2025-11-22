<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\EntregaReciclaje;
use App\Models\CanjeUsuario;

class PanelUsuarioController extends Controller
{
    public function dashboard()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $usuario = Auth::user();
        $usuarioId = (string) $usuario->getKey();

        $puntos = (int) ($usuario->puntos_acumulados ?? 0);

        $entregasAprobadas = EntregaReciclaje::where('id_usuario', $usuarioId)
            ->where('estado', 'aprobada')
            ->count();

        $canjes = CanjeUsuario::where('id_usuario', $usuarioId)->count();

        return view('panel_usuario_flujo', [
            'puntos'            => $puntos,
            'entregasAprobadas' => $entregasAprobadas,
            'canjes'            => $canjes,
            'active'           => 'resumen',
        ]);
    }
}
