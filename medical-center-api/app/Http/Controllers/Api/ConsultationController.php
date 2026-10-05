<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreConsultationRequest;
use App\Http\Requests\UpdateConsultationRequest;
use App\Models\Consultation;
use App\Services\ConsultationService;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\StoreConsultationProcedureRequest;
use App\Http\Requests\UpdateConsultationStatusRequest;

class ConsultationController extends Controller
{
    public function __construct(
        private ConsultationService $consultationService
    ) {}

    public function index(): JsonResponse
    {
        return response()->json(
            $this->consultationService->getAll()
        );
    }

    public function store(
        StoreConsultationRequest $request
    ): JsonResponse {
        $consultation = $this->consultationService->create(
            $request->validated()
        );

        return response()->json(
            $consultation,
            201
        );
    }

    public function show(
        Consultation $consultation
    ): JsonResponse {
        return response()->json(
            $this->consultationService->getById(
                $consultation->id
            )
        );
    }

    public function update(
        UpdateConsultationRequest $request,
        Consultation $consultation
    ): JsonResponse {
        $consultation = $this->consultationService->update(
            $consultation,
            $request->validated()
        );

        return response()->json($consultation);
    }

    public function destroy(
        Consultation $consultation
    ): JsonResponse {
        $this->consultationService->delete($consultation);

        return response()->json([
            'message' => 'Consultation deleted successfully.'
        ]);
    }
    public function addProcedure(
        StoreConsultationProcedureRequest $request,
        Consultation $consultation
    ): JsonResponse {
        $procedure = $this->consultationService->addProcedure(
            $consultation,
            $request->validated()
        );

        return response()->json($procedure->load('procedure'), 201);
    }
    public function financialSummary(
        Consultation $consultation
    ): JsonResponse {
        return response()->json(
            $this->consultationService->getFinancialSummary(
                $consultation
            )
        );
    }
    public function updateStatus(
        UpdateConsultationStatusRequest $request,
        Consultation $consultation
    ): JsonResponse {
        try {
            $consultation = $this->consultationService->updateStatus(
                $consultation,
                $request->validated('status')
            );

            return response()->json($consultation);
        } catch (\LogicException $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 422);
        }
    }
    public function waiting(): JsonResponse
    {
        return response()->json(
            $this->consultationService->getWaiting()
        );
    }
}
