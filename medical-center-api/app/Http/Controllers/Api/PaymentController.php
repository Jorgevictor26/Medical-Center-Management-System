<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePaymentRequest;
use App\Models\Payment;
use App\Services\PaymentService;
use Illuminate\Http\JsonResponse;

class PaymentController extends Controller
{
    public function __construct(
        private PaymentService $paymentService
    ) {}

    public function index(): JsonResponse
    {
        return response()->json(
            $this->paymentService->getAll()
        );
    }

    public function store(
        StorePaymentRequest $request
    ): JsonResponse {
        try {
            $payment = $this->paymentService->create(
                $request->validated()
            );

            return response()->json($payment, 201);
        } catch (\LogicException $e) {
            return response()->json([
                'message' => $e->getMessage()
            ], 422);
        }
    }

    public function show(Payment $payment): JsonResponse
    {
        return response()->json(
            $this->paymentService->getById($payment->id)
        );
    }

    public function destroy(Payment $payment): JsonResponse
    {
        $this->paymentService->delete($payment);

        return response()->json([
            'message' => 'Payment deleted successfully.'
        ]);
    }
}
