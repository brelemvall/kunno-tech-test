<?php

namespace App\Http\Controllers;

use App\Models\Operation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OperationController extends Controller
{
    /**
     * TODO: Retornar todas las operaciones en formato JSON.
     * GET /api/operations
     */
    public function index(): JsonResponse
    {
        // Tu implementación aquí
    }

    /**
     * TODO: Validar el payload (ver MODULES.md), crear la operación
     *       con status 'pending' y retornarla con código 201.
     * POST /api/operations
     */
    public function store(Request $request): JsonResponse
    {
        // Tu implementación aquí
    }

    /**
     * TODO: Validar que la operación exista (404 si no) y que su status
     *       sea 'pending' (422 si no). Despachar ProcessCommissionPayment
     *       y retornar 202 con { "message": "Processing queued" }.
     * POST /api/operations/{operation}/process
     */
    public function process(Operation $operation): JsonResponse
    {
        // Tu implementación aquí
    }
}
