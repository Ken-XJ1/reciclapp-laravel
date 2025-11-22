<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Usuario;
use App\Models\PuntoReciclaje;
use App\Models\ReporteRecoleccion;
use App\Models\Premio;

class AdminController extends Controller
{
    public function dashboard()
    {
        // Traemos los datos para las estadísticas
        $usuarios = Usuario::count();
        $puntos = PuntoReciclaje::count();
        $reportes = ReporteRecoleccion::count();
        $premios = Premio::count();

        return view('admin.dashboard', compact('usuarios', 'puntos', 'reportes', 'premios'));
    }
}
