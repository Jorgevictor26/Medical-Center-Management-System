<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDoctorRequest;
use App\Http\Requests\UpdateDoctorRequest;
use App\Models\Doctor;
use App\Services\DoctorService;
use Illuminate\Http\JsonResponse;

class DoctorController extends Controller
{
    public function __construct(
        private DoctorService $doctorService
    ) {}

    public function index(): JsonResponse
    {
        return response()->json(
            $this->doctorService->getAll()
        );
    }

    public function store(StoreDoctorRequest $request): JsonResponse
    {
        $doctor = $this->doctorService->create(
            $request->validated()
        );

        return response()->json($doctor, 201);
    }

    public function show(Doctor $doctor): JsonResponse
    {
        return response()->json(
            $this->doctorService->getById($doctor->id)
        );
    }

    public function update(
        UpdateDoctorRequest $request,
        Doctor $doctor
    ): JsonResponse {
        $doctor = $this->doctorService->update(
            $doctor,
            $request->validated()
        );

        return response()->json($doctor);
    }

    public function destroy(Doctor $doctor): JsonResponse
    {
        $this->doctorService->delete($doctor);

        return response()->json([
            'message' => 'Doctor deleted successfully.'
        ]);
    }
}
