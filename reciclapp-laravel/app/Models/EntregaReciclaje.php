<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model as Eloquent;

class EntregaReciclaje extends Eloquent
{
    protected $connection = 'mongodb';
    protected $collection = 'entregas_reciclaje';

    protected $fillable = [
        'id_usuario',
        'tipo_residuo',
        'cantidad_kg',
        'puntos_sugeridos',
        'puntos_otorgados',
        'estado', // pendiente, aprobada, rechazada
        'fecha_registro',
        'fecha_validacion',
        'observaciones_admin',
        'descripcion_general_materiales',
        'fecha_recoleccion_usuario',
        'ip_reporte',
        'ubicacion_recoleccion',
    ];
}
