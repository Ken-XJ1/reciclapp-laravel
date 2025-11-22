<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDB\Laravel\Eloquent\Model as Eloquent;
use Illuminate\Support\Facades\Auth;

class PuntoReciclaje extends Eloquent
{
    use HasFactory;

    protected $connection = 'mongodb';
    protected $collection = 'puntos_reciclaje';

    protected $fillable = [
        'nombre',
        'tipo',
        'direccion',
        'horario',
        'descripcion',
        'fecha_registro',
        'estado',
        'id_usuario_propone',
        'problemas',
    ];

    /**
     * Registrar una nueva propuesta de punto de reciclaje desde el panel de usuario.
     */
    public static function registrarPropuesta(array $data): self
    {
        $usuarioId = Auth::id();

        return self::create([
            'nombre'             => $data['nombre'] ?? null,
            'tipo'               => $data['tipo'] ?? null,
            'direccion'          => $data['direccion'] ?? null,
            'horario'            => $data['horario'] ?? null,
            'descripcion'        => $data['descripcion'] ?? null,
            'fecha_registro'     => now(),
            'estado'             => 'pendiente',
            'id_usuario_propone' => $usuarioId ? (string) $usuarioId : null,
        ]);
    }

    /**
     * Agregar un problema reportado sobre este punto.
     */
    public function agregarProblema(string $tipoProblema, ?string $descripcion = null): void
    {
        $problemas = $this->problemas ?? [];

        $problemas[] = [
            'tipo'        => $tipoProblema,
            'descripcion' => $descripcion,
            'fecha'       => now(),
        ];

        $this->problemas = $problemas;
        $this->save();
    }
}
