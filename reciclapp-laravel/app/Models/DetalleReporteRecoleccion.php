<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model as Eloquent;

class DetalleReporteRecoleccion extends Eloquent
{
    protected $connection = 'mongodb';
    protected $collection = 'detalle_reporte_recoleccion';

    protected $fillable = [
        'id_reporte',
        'tipo_residuo',
        'peso',
        'unidad',
    ];
}
