<?php

namespace App\Services;

use App\Models\Consultation;
use App\Repositories\ConsultationRepository;
use Illuminate\Database\Eloquent\Collection;
use App\Models\Procedure;
use App\Models\ConsultationProcedure;

class ConsultationService
{
    public function __construct(
        private ConsultationRepository $consultationRepository
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
        return $this->consultationRepository->update(
            $consultation,
            $data
        );
    }

    public function delete(Consultation $consultation): void
    {
        $this->consultationRepository->delete($consultation);
    }
    public function addProcedure(
        Consultation $consultation,
        array $data
    ): ConsultationProcedure {
        if ($consultation->status === 'completed') {
            throw new \LogicException(
                'Cannot add procedures to a completed consultation.'
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
}
