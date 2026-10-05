<?php

namespace App\Repositories;

use App\Models\Payment;
use Illuminate\Database\Eloquent\Collection;

class PaymentRepository
{
    public function all(): Collection
    {
        return Payment::with('consultation')
            ->latest('payment_date')
            ->get();
    }

    public function find(int $id): Payment
    {
        return Payment::with('consultation')
            ->findOrFail($id);
    }

    public function create(array $data): Payment
    {
        return Payment::create($data);
    }

    public function delete(Payment $payment): void
    {
        $payment->delete();
    }
}