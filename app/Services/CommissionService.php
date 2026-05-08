<?php

namespace App\Services;

use App\Models\Operation;

class CommissionService
{
    /**
     * Calcula y registra las comisiones de una operacion.
     *
     * @param  Operation $operation
     * @return array  Lista de commission_logs creados
     *
     * @throws \Exception si la operacion no esta en estado "open"
     * @throws \Exception si los porcentajes no suman 100%
     */
    public function calculate(Operation $operation): array
    {
        // TODO: implementar
        //
        // Pasos sugeridos:
        // 1. Validar que $operation->status === 'open'
        // 2. Cargar participantes de la operacion
        // 3. Validar que la suma de porcentajes sea exactamente 100
        // 4. Calcular base_amount con BCMath (no floats):
        //    $base = bcmul($operation->sale_value, bcdiv($rate, '100', 6), 2);
        // 5. Para cada participante calcular: base * (percentage / 100)
        // 6. Registrar cada calculo en commission_logs con status = 'pending'
        // 7. Actualizar operation->status = 'closed', closed_at = now()
        // 8. Todo dentro de DB::transaction()
        //
        // Retornar array con los logs creados

        throw new \RuntimeException('CommissionService::calculate() pendiente de implementar.');
    }
}
