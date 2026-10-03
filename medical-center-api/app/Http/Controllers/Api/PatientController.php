<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePatientRequest;
use App\Http\Requests\UpdatePatientRequest;
use App\Models\Patient;
use App\Services\PatientService;
use Illuminate\Http\JsonResponse;

class PatientController extends Controller
{
    public function __construct(
        private PatientService $patientService
    ) {}

    public function index(): JsonResponse
    {
        return response()->json(
            $this->patientService->getAll()
        );
    }

    public function store(StorePatientRequest $request): JsonResponse
    {
        $patient = $this->patientService->create(
            $request->validated()
        );

        return response()->json($patient, 201);
    }

    public function show(Patient $patient): JsonResponse
    {
        return response()->json(
            $this->patientService->getById($patient->id)
        );
    }

    public function update(
        UpdatePatientRequest $request,
        Patient $patient
    ): JsonResponse {
        $patient = $this->patientService->update(
            $patient,
            $request->validated()
        );

        return response()->json($patient);
    }

    public function destroy(Patient $patient): JsonResponse
    {
        $this->patientService->delete($patient);

        return response()->json([
            'message' => 'Patient deleted successfully.'
        ]);
    }
}
    