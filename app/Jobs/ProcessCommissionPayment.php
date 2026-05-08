<?php

namespace App\Jobs;

use App\Models\Operation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessCommissionPayment implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Numero maximo de reintentos si el Job falla.
     * TODO: confirmar si 3 es el valor correcto
     */
    public int $tries = 3;

    public function __construct(
        private readonly Operation $operation
    ) {}

    /**
     * TODO: implementar
     *
     * Pasos:
     * 1. Cambiar estado de commission_logs a "processing"
     * 2. Simular llamada a Stripe (sleep(1) o throw aleatorio)
     * 3. Exito: cambiar estado a "paid"
     * 4. Fallo: cambiar a "failed" y relanzar excepcion para reintento
     */
    public function handle(): void
    {
        throw new \RuntimeException('ProcessCommissionPayment::handle() pendiente.');
    }

    /**
     * Se ejecuta cuando se agotan todos los reintentos.
     * TODO: que hacer cuando fallan los 3 intentos
     */
    public function failed(\Throwable $exception): void
    {
        // TODO: implementar
    }
}
