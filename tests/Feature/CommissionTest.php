<?php

namespace Tests\Feature;

use App\Jobs\ProcessCommissionPayment;
use App\Models\Agency;
use App\Models\Operation;
use App\Services\CommissionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class CommissionTest extends TestCase
{
    use RefreshDatabase;

    // -------------------------------------------------------------------------
    // Módulo 1 — Modelo
    // -------------------------------------------------------------------------

    /** @test */
    public function it_creates_an_operation_with_pending_status(): void
    {
        $agency    = Agency::factory()->create();
        $operation = Operation::create([
            'agency_id'       => $agency->id,
            'amount'          => 1000.00,
            'currency'        => 'EUR',
            'commission_rate' => 0.025,
            'status'          => 'pending',
        ]);

        $this->assertDatabaseHas('operations', [
            'id'     => $operation->id,
            'status' => 'pending',
        ]);
    }

    /** @test */
    public function it_filters_pending_operations_with_scope(): void
    {
        $agency = Agency::factory()->create();
        Operation::factory()->count(2)->create(['agency_id' => $agency->id, 'status' => 'pending']);
        Operation::factory()->count(3)->create(['agency_id' => $agency->id, 'status' => 'processed']);

        $this->assertCount(2, Operation::pending()->get());
    }

    // -------------------------------------------------------------------------
    // Módulo 2 — CommissionService
    // -------------------------------------------------------------------------

    /** @test */
    public function it_calculates_commission_correctly(): void
    {
        $agency    = Agency::factory()->create();
        $operation = Operation::factory()->create([
            'agency_id'       => $agency->id,
            'amount'          => 2000.00,
            'commission_rate' => 0.025,
        ]);

        $service = new CommissionService();
        $result  = $service->calculate($operation);

        $this->assertEquals(50.00, $result);
    }

    /** @test */
    public function it_throws_exception_for_invalid_amount(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $agency    = Agency::factory()->create();
        $operation = Operation::factory()->make([
            'agency_id' => $agency->id,
            'amount'    => -100,
        ]);

        (new CommissionService())->calculate($operation);
    }

    /** @test */
    public function it_throws_exception_for_out_of_range_commission_rate(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $agency    = Agency::factory()->create();
        $operation = Operation::factory()->make([
            'agency_id'       => $agency->id,
            'commission_rate' => 1.5,
        ]);

        (new CommissionService())->calculate($operation);
    }

    /** @test */
    public function it_processes_an_operation_and_marks_it_as_processed(): void
    {
        $agency    = Agency::factory()->create();
        $operation = Operation::factory()->create([
            'agency_id'       => $agency->id,
            'amount'          => 1000.00,
            'commission_rate' => 0.05,
            'status'          => 'pending',
        ]);

        (new CommissionService())->process($operation);

        $this->assertEquals('processed', $operation->fresh()->status);
        $this->assertEquals(50.00, $operation->fresh()->commission_amount);
        $this->assertNotNull($operation->fresh()->processed_at);
    }

    // -------------------------------------------------------------------------
    // Módulo 3 — Controller & Job
    // -------------------------------------------------------------------------

    /** @test */
    public function it_lists_operations(): void
    {
        $agency = Agency::factory()->create();
        Operation::factory()->count(3)->create(['agency_id' => $agency->id]);

        $this->getJson('/api/operations')
             ->assertOk()
             ->assertJsonCount(3);
    }

    /** @test */
    public function it_creates_an_operation_via_api(): void
    {
        $agency = Agency::factory()->create();

        $this->postJson('/api/operations', [
            'agency_id'       => $agency->id,
            'amount'          => 500.00,
            'currency'        => 'USD',
            'commission_rate' => 0.03,
        ])->assertCreated()
          ->assertJsonFragment(['status' => 'pending']);
    }

    /** @test */
    public function it_dispatches_job_when_processing_pending_operation(): void
    {
        Queue::fake();

        $agency    = Agency::factory()->create();
        $operation = Operation::factory()->create([
            'agency_id' => $agency->id,
            'status'    => 'pending',
        ]);

        $this->postJson("/api/operations/{$operation->id}/process")
             ->assertAccepted()
             ->assertJson(['message' => 'Processing queued']);

        Queue::assertPushed(ProcessCommissionPayment::class);
    }

    /** @test */
    public function it_returns_422_when_processing_non_pending_operation(): void
    {
        $agency    = Agency::factory()->create();
        $operation = Operation::factory()->create([
            'agency_id' => $agency->id,
            'status'    => 'processed',
        ]);

        $this->postJson("/api/operations/{$operation->id}/process")
             ->assertUnprocessable();
    }
}
