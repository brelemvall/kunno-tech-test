<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    /**
     * NO MODIFICAR — datos de prueba base.
     */
    public function run(): void
    {
        DB::table('agencies')->insert([
            [
                'name'                    => 'Merida Propiedades',
                'slug'                    => 'merida-propiedades',
                'default_commission_rate' => 3.00,
                'created_at'              => now(),
                'updated_at'              => now(),
            ],
            [
                'name'                    => 'Cancun Inmuebles',
                'slug'                    => 'cancun-inmuebles',
                'default_commission_rate' => 2.50,
                'created_at'              => now(),
                'updated_at'              => now(),
            ],
        ]);

        // Solo inserta operacion de prueba si el candidato ya creo las tablas
        if (Schema::hasTable('operations') && Schema::hasTable('commission_participants')) {
            $operationId = DB::table('operations')->insertGetId([
                'agency_id'        => 1,
                'property_address' => 'Calle 60 Norte 432, Altabrisa, Merida',
                'sale_value'       => 500000.00,
                'status'           => 'open',
                'closed_at'        => null,
                'created_at'       => now(),
                'updated_at'       => now(),
            ]);

            DB::table('commission_participants')->insert([
                ['operation_id' => $operationId, 'role' => 'captador',    'participant_name' => 'Ana Garcia Lopez',    'percentage' => 30.00, 'created_at' => now(), 'updated_at' => now()],
                ['operation_id' => $operationId, 'role' => 'vendedor',    'participant_name' => 'Carlos Mendez Ruiz',  'percentage' => 40.00, 'created_at' => now(), 'updated_at' => now()],
                ['operation_id' => $operationId, 'role' => 'coordinador', 'participant_name' => 'Maria Torres Chan',   'percentage' => 10.00, 'created_at' => now(), 'updated_at' => now()],
                ['operation_id' => $operationId, 'role' => 'agencia',     'participant_name' => 'Merida Propiedades',  'percentage' => 20.00, 'created_at' => now(), 'updated_at' => now()],
            ]);
        }
    }
}
