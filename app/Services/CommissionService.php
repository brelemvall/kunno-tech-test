<?php

namespace App\Services;

use App\Models\Operation;

class CommissionService
{
    /**
     * TODO — Paso 1: Validar que $operation->amount sea mayor que 0.
     *         Lanzar \InvalidArgumentException si no lo es.
     *
     * TODO — Paso 2: Validar que $operation->commission_rate esté en [0.0001, 0.9999].
     *         Lanzar \InvalidArgumentException si está fuera de rango.
     *
     * TODO — Paso 3: Calcular commission_amount = amount × commission_rate.
     *         Redondear a 2 decimales y retornar el valor.
     */
    public function calculate(Operation $operation): float
    {
        // Tu implementación aquí
    }

    /**
     * TODO — Paso 1: Llamar a calculate() y asignar el resultado a $operation->commission_amount.
     *
     * TODO — Paso 2: Actualizar $operation->status a 'processed'.
     *
     * TODO — Paso 3: Registrar $operation->processed_at = now().
     *
     * TODO — Paso 4: Persistir con $operation->save().
     *
     * TODO — Paso 5: Envolver todo en try/catch. Si hay excepción,
     *         actualizar status a 'failed', guardar y relanzar la excepción.
     */
    public function process(Operation $operation): void
    {
        // Tu implementación aquí
    }
}
