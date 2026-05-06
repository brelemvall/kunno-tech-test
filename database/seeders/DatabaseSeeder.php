<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Agencia 1 — tasa estándar
        DB::table('agencies')->insert([
            'name'                    => 'Agencia Norte',
            'code'                    => 'AGN-001',
            'default_commission_rate' => 0.0250,
            'active'                  => true,
            'created_at'              => now(),
            'updated_at'              => now(),
        ]);

        // Agencia 2 — tasa premium
        DB::table('agencies')->insert([
            'name'                    => 'Agencia Sur',
            'code'                    => 'AGS-002',
            'default_commission_rate' => 0.0150,
            'active'                  => true,
            'created_at'              => now(),
            'updated_at'              => now(),
        ]);

        // Operación de prueba en estado pending
        DB::table('operations')->insert([
            'agency_id'         => 1,
            'amount'            => 2000.00,
            'currency'          => 'EUR',
            'commission_rate'   => 0.0250,
            'commission_amount' => null,
            'status'            => 'pending',
            'processed_at'      => null,
            'created_at'        => now(),
            'updated_at'        => now(),
        ]);
    }
}
