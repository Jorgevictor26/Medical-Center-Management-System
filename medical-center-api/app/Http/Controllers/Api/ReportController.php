<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ReportRequest;
use App\Services\ReportService;
use Illuminate\Http\JsonResponse;

class ReportController extends Controller
{
    public function __construct(
        private ReportService $reportService
    ) {}

    public function index(ReportRequest $request): JsonResponse
    {
        $validated = $request->validated();

        return response()->json(
            $this->reportService->generate(
                $validated['from'],
                $validated['to']
            )
        );
    }
}
