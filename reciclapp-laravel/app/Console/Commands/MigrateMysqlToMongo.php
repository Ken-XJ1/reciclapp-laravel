<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use MongoDB\Client as MongoClient;

class MigrateMysqlToMongo extends Command
{
    protected $signature = 'migrate:mysql-to-mongo';
    protected $description = 'Migra los datos desde MySQL hacia MongoDB';

    public function handle()
    {
        $this->info('Iniciando migración de MySQL a MongoDB...');

        
        $mysql = DB::connection('mysql');

        
        $mongoClient = new MongoClient("mongodb://127.0.0.1:27017");
        $mongo = $mongoClient->selectDatabase(env('DB_DATABASE', 'reciclapp'));

        
        $tablas = [
            'usuarios',
            'premios',
            'canjes_usuario',
            'puntos_de_reciclaje',
            'reportes_recoleccion_usuario',
            'detalle_reporte_recoleccion',
            'tipos_residuos',
            'logros',
            'usuario_logros_obtenidos',
            'auditoria',
        ];

        foreach ($tablas as $tabla) {
            $this->info("Migrando tabla: {$tabla}");

            
            $datos = $mysql->table($tabla)->get();

            
            $coleccion = $mongo->selectCollection($tabla);
            $coleccion->drop(); // Limpia colección existente
            $registros = json_decode(json_encode($datos), true);

            if (!empty($registros)) {
                $coleccion->insertMany($registros);
                $this->info(" Insertados " . count($registros) . " registros en {$tabla}");
            } else {
                $this->warn(" No hay datos en {$tabla}");
            }
        }

        $this->info('🚀 Migración completada exitosamente.');
        return 0;
    }
}
