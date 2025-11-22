<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PuntoReciclaje;
use App\Models\EntregaReciclaje;
use App\Models\ReporteRecoleccion;
use Illuminate\Support\Facades\Auth;
use App\Models\Auditoria;

class ReciclajeController extends Controller
{
    // Mostrar la vista principal de "Gestionar mi reciclaje"
    public function index()
    {
        return view('panel.reciclaje', [
            'active' => 'reciclaje',
        ]);
    }

    // Mostrar el formulario para proponer un nuevo punto
    public function proponer()
    {
        return view('reciclaje.proponer');
    }

    // Guardar la propuesta de un nuevo punto
    public function guardarPropuesta(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:255',
            'tipo' => 'required|string|max:100',
            'direccion' => 'required|string|max:255',
            'horario' => 'nullable|string|max:100',
            'descripcion' => 'nullable|string|max:500',
        ]);

        PuntoReciclaje::registrarPropuesta($request->all());

        // Auditoría: usuario propone un nuevo punto de reciclaje
        $usuario = Auth::user();
        Auditoria::create([
            'accion'          => 'crear_propuesta_punto',
            'usuario'         => $usuario->email ?? 'invitado',
            'fecha'           => now(),
            'descripcion'     => 'Propuesta de punto: ' . ($request->nombre ?? '') . ' - ' . ($request->direccion ?? ''),
            'modulo_afectado' => 'panel_reciclaje',
            'resultado'       => 'exitoso',
            'ip'              => $request->ip(),
        ]);

        return back()->with('success', 'Propuesta registrada correctamente.');
    }

    // Mostrar el formulario para reportar un problema
    public function reportar()
    {
        $puntos = PuntoReciclaje::all();
        return view('reciclaje.reportar', compact('puntos'));
    }

    // Guardar el reporte de un problema
    public function guardarReporte(Request $request)
    {
        $request->validate([
            'punto_id' => 'required',
            'tipo_problema' => 'required|string|max:100',
            'descripcion' => 'nullable|string|max:500',
            'descripcion_otro' => 'nullable|string|max:500',
        ]);

        $punto = PuntoReciclaje::find($request->punto_id);
        if ($punto) {
            $descripcionProblema = $request->descripcion;
            if ($request->tipo_problema === 'otros' && $request->filled('descripcion_otro')) {
                $descripcionProblema = $request->descripcion_otro;
            }

            $punto->agregarProblema($request->tipo_problema, $descripcionProblema);
        }

        $usuarioId = Auth::id();

        $observaciones = 'Tipo de problema: ' . $request->tipo_problema;
        if (!empty($descripcionProblema)) {
            $observaciones .= ' | Descripción: ' . $descripcionProblema;
        }

        ReporteRecoleccion::create([
            'id_usuario'    => $usuarioId ? (string) $usuarioId : null,
            'id_punto'      => (string) $request->punto_id,
            'fecha_reporte' => now(),
            'estado'        => 'pendiente',
            'observaciones' => $observaciones,
        ]);

        // Auditoría: usuario reporta un problema en un punto de reciclaje
        $usuario = Auth::user();
        Auditoria::create([
            'accion'          => 'reportar_problema_punto',
            'usuario'         => $usuario->email ?? 'invitado',
            'fecha'           => now(),
            'descripcion'     => 'Problema en punto ID ' . $request->punto_id . ' tipo: ' . $request->tipo_problema,
            'modulo_afectado' => 'panel_reciclaje',
            'resultado'       => 'exitoso',
            'ip'              => $request->ip(),
        ]);

        return back()->with('success', 'Problema reportado correctamente.');
    }

    // Alias genérico para la ruta /reciclaje/guardar (por ahora usado para registrar entrega)
    public function guardar(Request $request)
    {
        return $this->guardarEntrega($request);
    }

    // Mostrar el formulario para registrar una entrega de reciclaje
    public function registrar()
    {
        return view('reciclaje.registrar');
    }

    // Guardar los datos de una entrega de reciclaje
    public function guardarEntrega(Request $request)
    {
        $request->validate([
            'tipo_residuo' => 'required|string|max:100',
            'cantidad' => 'required|numeric|min:0.1',
            'descripcion_general_materiales' => 'nullable|string|max:500',
            'fecha_recoleccion_usuario' => 'nullable|date',
            'ubicacion_recoleccion' => 'nullable|string|max:255',
        ]);

        $usuarioId = Auth::id();

        if (!$usuarioId) {
            return redirect()->route('login');
        }

        $cantidadKg = (float) $request->cantidad; // el formulario trabaja en KG
        $tipo = $request->tipo_residuo;
        $puntosSugeridos = $this->calcularPuntosPorKg($tipo, $cantidadKg);

        $entrega = EntregaReciclaje::create([
            'id_usuario'                  => (string) $usuarioId,
            'tipo_residuo'                => $tipo,
            'cantidad_kg'                 => $cantidadKg,
            'puntos_sugeridos'            => $puntosSugeridos,
            'puntos_otorgados'            => 0,
            'estado'                      => 'pendiente',
            'fecha_registro'              => now(),
            'descripcion_general_materiales' => $request->descripcion_general_materiales,
            'fecha_recoleccion_usuario'   => $request->fecha_recoleccion_usuario,
            'ip_reporte'                  => $request->ip(),
            'ubicacion_recoleccion'       => $request->ubicacion_recoleccion,
        ]);

        // Auditoría: usuario registra una entrega de reciclaje
        $usuario = Auth::user();
        Auditoria::create([
            'accion'          => 'registrar_entrega_reciclaje',
            'usuario'         => $usuario->email ?? 'invitado',
            'fecha'           => now(),
            'descripcion'     => 'Entrega tipo ' . $tipo . ' (' . $cantidadKg . ' kg), puntos sugeridos: ' . $puntosSugeridos,
            'modulo_afectado' => 'entregas_reciclaje',
            'resultado'       => 'pendiente_validacion',
            'ip'              => $request->ip(),
        ]);

        return back()->with('success', 'Entrega registrada y enviada al administrador para validación.');
    }

    /**
     * Calcular puntos sugeridos según tipo de residuo y cantidad en KG.
     */
    protected function calcularPuntosPorKg(string $tipoResiduo, float $kg): int
    {
        $tipo = mb_strtolower($tipoResiduo);

        // Tabla simple de puntos por kg según material
        $tabla = [
            'plastico'  => 10,
            'plástico'  => 10,
            'pet'       => 12,
            'papel'     => 8,
            'carton'    => 8,
            'cartón'    => 8,
            'vidrio'    => 6,
            'metal'     => 12,
            'aluminio'  => 12,
            'organico'  => 4,
            'orgánico'  => 4,
        ];

        $puntosPorKg = 5; // valor por defecto

        foreach ($tabla as $clave => $valor) {
            if (str_contains($tipo, $clave)) {
                $puntosPorKg = $valor;
                break;
            }
        }

        return (int) round($kg * $puntosPorKg);
    }
}
