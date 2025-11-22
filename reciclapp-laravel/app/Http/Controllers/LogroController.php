<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Logro; // Debes crear este modelo
use App\Models\Auditoria;

class LogroController extends Controller
{
    public function index()
    {
        $logros = Logro::orderBy('puntos_requeridos')->get();
        return view('admin.logros', compact('logros'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre'            => 'required|string|max:255',
            'descripcion'       => 'nullable|string|max:1000',
            'puntos_requeridos' => 'required|integer|min:1',
            'icono'             => 'nullable|string|max:255',
        ]);

        $logro = Logro::create($data);

        // Auditoría: crear logro
        $admin = auth()->user();
        Auditoria::create([
            'accion'          => 'crear_logro',
            'usuario'         => $admin->email ?? 'admin',
            'fecha'           => now(),
            'descripcion'     => 'Logro creado: ' . ($logro->nombre ?? ''),
            'modulo_afectado' => 'logros_admin',
            'resultado'       => 'exitoso',
            'ip'              => $request->ip(),
        ]);

        return redirect()->route('admin.logros')->with('success', 'Logro creado correctamente');
    }

    public function update(Request $request, $id)
    {
        $logro = Logro::find($id);

        if (!$logro) {
            return redirect()->route('admin.logros')->with('error', 'El logro no existe.');
        }

        $data = $request->validate([
            'nombre'            => 'required|string|max:255',
            'descripcion'       => 'nullable|string|max:1000',
            'puntos_requeridos' => 'required|integer|min:1',
            'icono'             => 'nullable|string|max:255',
        ]);

        $logro->update($data);

        // Auditoría: actualizar logro
        $admin = auth()->user();
        Auditoria::create([
            'accion'          => 'actualizar_logro',
            'usuario'         => $admin->email ?? 'admin',
            'fecha'           => now(),
            'descripcion'     => 'Logro actualizado: ' . ($logro->nombre ?? '') . ' (ID ' . $logro->_id . ')',
            'modulo_afectado' => 'logros_admin',
            'resultado'       => 'exitoso',
            'ip'              => $request->ip(),
        ]);

        return redirect()->route('admin.logros')->with('success', 'Logro actualizado correctamente');
    }

    public function destroy(Request $request, $id)
    {
        $logro = Logro::find($id);

        if ($logro) {
            $nombre = $logro->nombre;
            $logro->delete();

            // Auditoría: eliminar logro
            $admin = auth()->user();
            Auditoria::create([
                'accion'          => 'eliminar_logro',
                'usuario'         => $admin->email ?? 'admin',
                'fecha'           => now(),
                'descripcion'     => 'Logro eliminado: ' . ($nombre ?? '') . ' (ID ' . $logro->_id . ')',
                'modulo_afectado' => 'logros_admin',
                'resultado'       => 'exitoso',
                'ip'              => $request->ip(),
            ]);
        }

        return redirect()->route('admin.logros')->with('success', 'Logro eliminado correctamente');
    }
}
