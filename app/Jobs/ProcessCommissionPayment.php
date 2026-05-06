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

    // TODO: Definir número máximo de intentos
    // public int $tries = ?;

    // TODO: Definir backoff en segundos entre reintentos
    // public int $backoff = ?;

    // TODO: Inyectar Operation en el constructor

    /**
     * TODO: Inyectar CommissionService y llamar a process().
     *       Si falla tras los intentos, loguear el error con Log::error().
     */
    public function handle(): void
    {
        // Tu implementación aquí
    }
}
