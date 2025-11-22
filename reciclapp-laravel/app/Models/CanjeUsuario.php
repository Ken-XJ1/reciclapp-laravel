<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model as Eloquent;

class CanjeUsuario extends Eloquent
{
    protected $connection = 'mongodb';
    protected $collection = 'canjes_usuario';

    protected $fillable = [
        'id_usuario',
        'id_premio',
        'fecha_canje',
        'puntos_usados',
        'codigo_canje_fisico',
        'estado_canje', // pendiente_entrega, entregado, cancelado
        'fecha_entrega',
        'observaciones_entrega',
    ];
}
