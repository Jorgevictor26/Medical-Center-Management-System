<?php

namespace App\Services;

use App\Models\Consultation;
use App\Repositories\ConsultationRepository;
use Illuminate\Database\Eloquent\Collection;

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
}