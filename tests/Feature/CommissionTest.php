<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * NO MODIFICAR — hacer que estos tests pasen es el objetivo de la prueba.
 *
 * Correr con: php artisan test --filter CommissionTest
 *
 * Estado inicial esperado: todos FAILING — eso es correcto.
 * Estado final esperado:   todos PASSING — ese es el objetivo.
 */
class CommissionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    // ── MODULO 1 — Modelo de datos ────────────────────────────────────────

    /** @test */
    public function it_has_the_required_tables(): void
    {
        $this->assertTrue(Schema::hasTable('agencies'), 'Tabla agencies debe existir');
        $this->assertTrue(Schema::hasTable('operations'), 'Tabla operations debe existir');
        $this->assertTrue(Schema::hasTable('commission_participants'), 'Tabla commission_participants debe existir');
        $this->assertTrue(Schema::hasTable('commission_logs'), 'Tabla commission_logs debe existir');
    }

    /** @test */
    public function it_has_correct_columns_in_operations(): void
    {
        $required = ['id', 'agency_id', 'property_address', 'sale_value', 'status', 'closed_at'];
        foreach ($required as $column) {
            $this->assertTrue(
                Schema::hasColumn('operations', $column),
                "La tabla operations debe tener la columna: {$column}"
            );
        }
    }

    /** @test */
    public function it_has_correct_columns_in_commission_logs(): void
    {
        $required = [
            'id', 'operation_id', 'participant_role', 'participant_name',
            'base_amount', 'percentage_applied', 'calculated_amount',
            'status', 'calculated_at'
        ];
        foreach ($required as $column) {
            $this->assertTrue(
                Schema::hasColumn('commission_logs', $column),
                "La tabla commission_logs debe tener la columna: {$column}"
            );
        }
    }

    // ── MODULO 2 — CommissionService ──────────────────────────────────────

    /** @test */
    public function it_calculates_commissions_correctly(): void
    {
        // Operacion del seeder: $500,000 MXN, tasa 3%, base = $15,000
        $operation = \App\Models\Operation::where('status', 'open')->first();
        $this->assertNotNull($operation, 'Debe existir una operacion open (revisar seeder y migraciones)');

        $response = $this->postJson("/api/operations/{$operation->id}/close");
        $response->assertStatus(200);

        $logs = DB::table('commission_logs')->where('operation_id', $operation->id)->get();
        $this->assertCount(4, $logs, 'Deben existir 4 registros en commission_logs');

        $byRole = $logs->keyBy('participant_role');

        $this->assertEquals('4500.00', number_format((float)$byRole['captador']->calculated_amount, 2, '.', ''),
            'Captador (30%) debe recibir $4,500.00');
        $this->assertEquals('6000.00', number_format((float)$byRole['vendedor']->calculated_amount, 2, '.', ''),
            'Vendedor (40%) debe recibir $6,000.00');
        $this->assertEquals('1500.00', number_format((float)$byRole['coordinador']->calculated_amount, 2, '.', ''),
            'Coordinador (10%) debe recibir $1,500.00');
        $this->assertEquals('3000.00', number_format((float)$byRole['agencia']->calculated_amount, 2, '.', ''),
            'Agencia (20%) debe recibir $3,000.00');
    }

    /** @test */
    public function it_rejects_invalid_operation_status(): void
    {
        $operation = \App\Models\Operation::where('status', 'open')->first();
        $this->assertNotNull($operation);

        // Primer cierre — debe funcionar
        $this->postJson("/api/operations/{$operation->id}/close")->assertStatus(200);

        // Segundo cierre — debe rechazarse con 409
        $this->postJson("/api/operations/{$operation->id}/close")->assertStatus(409);
    }

    /** @test */
    public function it_rejects_percentages_that_dont_sum_100(): void
    {
        $agency = DB::table('agencies')->first();

        $operationId = DB::table('operations')->insertGetId([
            'agency_id'        => $agency->id,
            'property_address' => 'Calle de prueba 1',
            'sale_value'       => 300000.00,
            'status'           => 'open',
            'closed_at'        => null,
            'created_at'       => now(),
            'updated_at'       => now(),
        ]);

        // Solo 70% total — invalido
        DB::table('commission_participants')->insert([
            ['operation_id' => $operationId, 'role' => 'captador', 'participant_name' => 'Test A', 'percentage' => 40.00, 'created_at' => now(), 'updated_at' => now()],
            ['operation_id' => $operationId, 'role' => 'vendedor', 'participant_name' => 'Test B', 'percentage' => 30.00, 'created_at' => now(), 'updated_at' => now()],
        ]);

        $response = $this->postJson("/api/operations/{$operationId}/close");
        $response->assertStatus(422);

        // Verificar rollback — no deben existir logs
        $logsCount = DB::table('commission_logs')->where('operation_id', $operationId)->count();
        $this->assertEquals(0, $logsCount, 'No deben crearse logs si porcentajes son incorrectos');
    }

    /** @test */
    public function it_logs_each_commission_calculation(): void
    {
        $operation = \App\Models\Operation::where('status', 'open')->first();
        $this->assertNotNull($operation);

        $this->postJson("/api/operations/{$operation->id}/close")->assertStatus(200);

        $logs = DB::table('commission_logs')->where('operation_id', $operation->id)->get();

        foreach ($logs as $log) {
            $this->assertNotNull($log->base_amount, 'commission_log debe tener base_amount');
            $this->assertNotNull($log->percentage_applied, 'commission_log debe tener percentage_applied');
            $this->assertNotNull($log->calculated_amount, 'commission_log debe tener calculated_amount');
            $this->assertNotNull($log->calculated_at, 'commission_log debe tener calculated_at');
            $this->assertEquals('pending', $log->status, 'Estado inicial debe ser pending');
        }
    }

    /** @test */
    public function it_uses_database_transaction(): void
    {
        $agency = DB::table('agencies')->first();

        $operationId = DB::table('operations')->insertGetId([
            'agency_id'        => $agency->id,
            'property_address' => 'Test transaccion',
            'sale_value'       => 200000.00,
            'status'           => 'open',
            'closed_at'        => null,
            'created_at'       => now(),
            'updated_at'       => now(),
        ]);

        // Solo 50% — invalido, debe fallar y hacer rollback
        DB::table('commission_participants')->insert([
            ['operation_id' => $operationId, 'role' => 'captador', 'participant_name' => 'Solo', 'percentage' => 50.00, 'created_at' => now(), 'updated_at' => now()],
        ]);

        $this->postJson("/api/operations/{$operationId}/close")->assertStatus(422);

        // La operacion debe seguir en "open" — el rollback funciono
        $operation = DB::table('operations')->find($operationId);
        $this->assertEquals('open', $operation->status,
            'La operacion debe seguir en open si hubo error (rollback de transaccion)');

        $logsCount = DB::table('commission_logs')->where('operation_id', $operationId)->count();
        $this->assertEquals(0, $logsCount, 'No deben existir logs tras rollback');
    }

    /** @test */
    public function it_uses_precise_decimal_arithmetic(): void
    {
        // Valores que generan errores de punto flotante si se usan floats
        $agency = DB::table('agencies')->where('slug', 'merida-propiedades')->first();

        $operationId = DB::table('operations')->insertGetId([
            'agency_id'        => $agency->id,
            'property_address' => 'Test precision decimal',
            'sale_value'       => 333333.33,
            'status'           => 'open',
            'closed_at'        => null,
            'created_at'       => now(),
            'updated_at'       => now(),
        ]);

        DB::table('commission_participants')->insert([
            ['operation_id' => $operationId, 'role' => 'captador', 'participant_name' => 'A', 'percentage' => 33.33, 'created_at' => now(), 'updated_at' => now()],
            ['operation_id' => $operationId, 'role' => 'vendedor', 'participant_name' => 'B', 'percentage' => 33.33, 'created_at' => now(), 'updated_at' => now()],
            ['operation_id' => $operationId, 'role' => 'agencia',  'participant_name' => 'C', 'percentage' => 33.34, 'created_at' => now(), 'updated_at' => now()],
        ]);

        $this->postJson("/api/operations/{$operationId}/close")->assertStatus(200);

        $logs = DB::table('commission_logs')->where('operation_id', $operationId)->get();
        $totalCalculated = $logs->sum('calculated_amount');
        $baseAmount      = $logs->first()->base_amount;

        $this->assertLessThan(
            0.01,
            abs($totalCalculated - $baseAmount),
            'Los calculos deben usar BCMath (no floats). Diferencia detectada: ' .
            abs($totalCalculated - $baseAmount)
        );
    }

    // ── MODULO 3 — Resiliencia de pagos ───────────────────────────────────

    /** @test */
    public function it_dispatches_payment_job_asynchronously(): void
    {
        \Illuminate\Support\Facades\Queue::fake();

        $operation = \App\Models\Operation::where('status', 'open')->first();
        $this->assertNotNull($operation);

        $this->postJson("/api/operations/{$operation->id}/close")->assertStatus(200);

        $response = $this->postJson("/api/operations/{$operation->id}/pay");
        $response->assertStatus(202);

        \Illuminate\Support\Facades\Queue::assertPushed(
            \App\Jobs\ProcessCommissionPayment::class,
            'El Job ProcessCommissionPayment debe despacharse a la cola de forma asincrona'
        );
    }

    /** @test */
    public function it_rejects_payment_if_operation_is_not_closed(): void
    {
        $operation = \App\Models\Operation::where('status', 'open')->first();
        $this->assertNotNull($operation);

        // Intentar pagar sin cerrar primero — debe fallar con 409
        $response = $this->postJson("/api/operations/{$operation->id}/pay");
        $response->assertStatus(409);
    }
}
