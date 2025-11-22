<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDB\Laravel\Eloquent\Model as Eloquent;

class ReporteRecoleccion extends Eloquent
{
    use HasFactory;

    protected $connection = 'mongodb';
    protected $collection = 'reportes_recoleccion_usuario';

    protected $fillable = [
        'id_usuario',
        'id_punto',
        'fecha_reporte',
        'estado',
        'observaciones',
    ];
}
