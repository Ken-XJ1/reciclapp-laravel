<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReporteRecoleccionUsuario extends Model
{
    protected $table = 'reportes_recoleccion_usuario';
    protected $primaryKey = 'id_reporte';
    public $timestamps = false;
}
