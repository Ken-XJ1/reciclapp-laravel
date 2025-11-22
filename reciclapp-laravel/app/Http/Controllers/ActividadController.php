<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Actividad; // Debes crear este modelo

class ActividadController extends Controller
{
    public function index()
    {
        $actividades = Actividad::orderBy('fecha', 'desc')->get();
        return view('admin.actividades', compact('actividades'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'titulo'      => 'required|string|max:255',
            'descripcion' => 'nullable|string|max:2000',
            'fecha'       => 'required|date',
            'lugar'       => 'nullable|string|max:255',
            'estado'      => 'required|string|in:activa,finalizada,cancelada',
        ]);

        Actividad::create($data);

        return redirect()->route('admin.actividades')->with('success', 'Actividad creada correctamente');
    }

    public function update(Request $request, $id)
    {
        $actividad = Actividad::find($id);

        if (!$actividad) {
            return redirect()->route('admin.actividades')->with('error', 'La actividad no existe.');
        }

        $data = $request->validate([
            'titulo'      => 'required|string|max:255',
            'descripcion' => 'nullable|string|max:2000',
            'fecha'       => 'required|date',
            'lugar'       => 'nullable|string|max:255',
            'estado'      => 'required|string|in:activa,finalizada,cancelada',
        ]);

        $actividad->update($data);

        return redirect()->route('admin.actividades')->with('success', 'Actividad actualizada correctamente');
    }

    public function destroy($id)
    {
        $actividad = Actividad::find($id);

        if ($actividad) {
            $actividad->delete();
        }

        return redirect()->route('admin.actividades')->with('success', 'Actividad eliminada correctamente');
    }

    /**
     * Listado público para panel de usuario: solo actividades activas o próximas.
     */
    public function indexPublic()
    {
        $actividades = Actividad::where('estado', 'activa')
            ->orderBy('fecha', 'asc')
            ->get();

        return view('panel.actividades', compact('actividades'));
    }
}
