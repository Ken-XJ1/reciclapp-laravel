<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ReporteRecoleccion;

class ReporteController extends Controller
{
    public function index()
    {
        $reportes = ReporteRecoleccion::all();
        return view('admin.reportes', compact('reportes'));
    }

    public function show($id)
    {
        $reporte = ReporteRecoleccion::find($id);
        return view('admin.reporte_detalle', compact('reporte'));
    }

    public function resolver(Request $request, $id)
    {
        $reporte = ReporteRecoleccion::find($id);
        if($reporte){
            $reporte->estado = $request->estado ?? 'resuelto';
            $reporte->save();
        }
        return redirect()->route('admin.reportes')->with('success', 'Reporte actualizado');
    }
}
