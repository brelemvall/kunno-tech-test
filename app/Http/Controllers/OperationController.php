<?php

namespace App\Http\Controllers;

use App\Models\Operation;
use App\Services\CommissionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class OperationController extends Controller
{
    public function __construct(
        private readonly CommissionService $commissionService
    ) {}

    /**
     * POST /api/operations/{operation}/close
     * HTTP 200: logs de comisiones calculadas
     * HTTP 409: operacion no esta en "open"
     * HTTP 422: porcentajes no suman 100%
     */
    public function close(Operation $operation): JsonResponse
    {
        // TODO: implementar
        throw new \RuntimeException('OperationController::close() pendiente.');
    }

    /**
     * GET /api/operations/{operation}/commissions
     * HTTP 200: lista de commission_logs
     */
    public function commissions(Operation $operation): JsonResponse
    {
        // TODO: implementar
        throw new \RuntimeException('OperationController::commissions() pendiente.');
    }

    /**
     * POST /api/operations/{operation}/pay
     * Despacha Job ASINCRONO.
     * HTTP 202: respuesta inmediata con estado "processing"
     * HTTP 409: operacion no esta en "closed"
     */
    public function pay(Operation $operation): JsonResponse
    {
        // TODO: implementar
        throw new \RuntimeException('OperationController::pay() pendiente.');
    }
}
