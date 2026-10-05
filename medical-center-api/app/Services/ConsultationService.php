<?php

namespace App\Services;

use App\Models\Consultation;
use App\Repositories\ConsultationRepository;
use App\Repositories\PatientRepository;
use Illuminate\Database\Eloquent\Collection;
use App\Models\Procedure;
use App\Models\ConsultationProcedure;

class ConsultationService
{
    public function __construct(
        private ConsultationRepository $consultationRepository,
        private PatientRepository $patientRepository,
    ) {}

    public function getAll(): Collection
    {
        return $this->consultationRepository->all();
    }

    public function getById(int $id): Consultation
    {
        return $this->consultationRepository->find($id);
    }

    public function create(array $data): Consultation
    {
        $data['consultation_date'] = now();
        $data['status'] = 'waiting';

        return $this->consultationRepository->create($data);
    }

    public function update(
        Consultation $consultation,
        array $data
    ): Consultation {
        if (in_array($consultation->status, ['completed', 'cancelled'], true)) {
            throw new \LogicException(
                'Completed or cancelled consultations cannot be edited.'
            );
        }

        return $this->consultationRepository->update(
            $consultation,
            $data
        );
    }

    public function delete(Consultation $consultation): void
    {
        $this->consultationRepository->delete($consultation);
    }
    public function getWaiting(): Collection
    {
        return $this->consultationRepository->getWaiting();
    }
    public function addProcedure(
        Consultation $consultation,
        array $data
    ): ConsultationProcedure {
        if ($consultation->status !== 'in_progress') {
            throw new \LogicException(
                'Procedures can only be added while the consultation is in progress.'
            );
        }

        $procedure = Procedure::findOrFail($data['procedure_id']);

        if (!$procedure->status) {
            throw new \LogicException(
                'This procedure is inactive.'
            );
        }

        return $consultation->consultationProcedures()->create([
            'procedure_id' => $procedure->id,
            'quantity' => $data['quantity'],
            'unit_price' => $procedure->price,
            'tooth' => $data['tooth'] ?? null,
            'observation' => $data['observation'] ?? null,
        ]);
    }
    public function getFinancialSummary(
        Consultation $consultation
    ): array {
        $consultation->load([
            'consultationProcedures.procedure',
            'payments'
        ]);

        $total = $consultation->consultationProcedures->sum(
            fn($item) => $item->quantity * $item->unit_price
        );

        $paid = $consultation->payments->sum('amount');

        $outstanding = $total - $paid;

        if ($paid <= 0) {
            $paymentStatus = 'unpaid';
        } elseif ($outstanding > 0) {
            $paymentStatus = 'partially_paid';
        } else {
            $paymentStatus = 'paid';
        }

        return [
            'consultation' => $consultation,
            'total' => $total,
            'paid' => $paid,
            'outstanding' => $outstanding,
            'payment_status' => $paymentStatus,
        ];
    }
    public function updateStatus(
        Consultation $consultation,
        string $newStatus
    ): Consultation {
        $currentStatus = $consultation->status;

        $allowedTransitions = [
            'waiting' => ['in_progress', 'cancelled'],
            'in_progress' => ['completed', 'cancelled'],
            'completed' => [],
            'cancelled' => [],
        ];

        if (!in_array(
            $newStatus,
            $allowedTransitions[$currentStatus],
            true
        )) {
            throw new \LogicException(
                "Cannot change consultation status from {$currentStatus} to {$newStatus}."
            );
        }

        $consultation->update([
            'status' => $newStatus,
        ]);

        return $consultation->refresh();
    }
    public function getHistory(int $patientId): array
    {
        $patient = $this->patientRepository->getHistory($patientId);

        $total = 0;
        $paid = 0;

        foreach ($patient->consultations as $consultation) {

            $consultationTotal = $consultation->consultationProcedures->sum(
                fn($item) => $item->quantity * $item->unit_price
            );

            $consultationPaid = $consultation->payments->sum('amount');

            $consultation->total = $consultationTotal;
            $consultation->paid = $consultationPaid;
            $consultation->outstanding = $consultationTotal - $consultationPaid;

            $total += $consultationTotal;
            $paid += $consultationPaid;
        }

        return [
            'patient' => $patient,
            'total' => $total,
            'paid' => $paid,
            'outstanding' => $total - $paid,
        ];
    }
}
