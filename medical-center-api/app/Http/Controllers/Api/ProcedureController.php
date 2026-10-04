<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProcedureRequest;
use App\Http\Requests\UpdateProcedureRequest;
use App\Models\Procedure;
use App\Services\ProcedureService;
use Illuminate\Http\JsonResponse;

class ProcedureController extends Controller
{
    public function __construct(
        private ProcedureService $procedureService
    ) {}

    public function index(): JsonResponse
    {
        return response()->json(
            $this->procedureService->getAll()
        );
    }

    public function store(StoreProcedureRequest $request): JsonResponse
    {
        $procedure = $this->procedureService->create(
            $request->validated()
        );

        return response()->json($procedure, 201);
    }

    public function show(Procedure $procedure): JsonResponse
    {
        return response()->json(
            $this->procedureService->getById($procedure->id)
        );
    }

    public function update(
        UpdateProcedureRequest $request,
        Procedure $procedure
    ): JsonResponse {
        $procedure = $this->procedureService->update(
            $procedure,
            $request->validated()
        );

        return response()->json($procedure);
    }

    public function destroy(Procedure $procedure): JsonResponse
    {
        $this->procedureService->delete($procedure);

        return response()->json([
            'message' => 'Procedure deleted successfully.'
        ]);
    }
}
