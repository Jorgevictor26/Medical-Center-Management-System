<?php

namespace App\Services;

use App\Models\Consultation;
use App\Models\Payment;
use App\Repositories\PaymentRepository;
use Illuminate\Database\Eloquent\Collection;

class PaymentService
{
    public function __construct(
        private PaymentRepository $paymentRepository
    ) {}

    public function getAll(): Collection
    {
        return $this->paymentRepository->all();
    }

    public function getById(int $id): Payment
    {
        return $this->paymentRepository->find($id);
    }

    public function create(array $data): Payment
    {
        $consultation = Consultation::with([
            'consultationProcedures',
            'payments'
        ])->findOrFail($data['consultation_id']);

        $total = $consultation->consultationProcedures->sum(
            fn ($item) => $item->quantity * $item->unit_price
        );

        $paid = $consultation->payments->sum('amount');

        $outstanding = $total - $paid;

        if ($data['amount'] > $outstanding) {
            throw new \LogicException(
                'Payment cannot exceed the outstanding balance.'
            );
        }

        $data['payment_date'] = $data['payment_date'] ?? now();

        return $this->paymentRepository->create($data);
    }

    public function delete(Payment $payment): void
    {
        $this->paymentRepository->delete($payment);
    }
}