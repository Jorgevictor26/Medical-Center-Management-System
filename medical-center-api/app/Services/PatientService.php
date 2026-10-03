<?php

namespace App\Services;

use App\Models\Patient;
use App\Repositories\PatientRepository;
use Illuminate\Database\Eloquent\Collection;

class PatientService
{
    public function __construct(
        private PatientRepository $patientRepository
    ) {}

    public function getAll(): Collection
    {
        return $this->patientRepository->all();
    }

    public function getById(int $id): Patient
    {
        return $this->patientRepository->find($id);
    }

    public function create(array $data): Patient
    {
        return $this->patientRepository->create($data);
    }

    public function update(Patient $patient, array $data): Patient
    {
        return $this->patientRepository->update($patient, $data);
    }

    public function delete(Patient $patient): void
    {
        $this->patientRepository->delete($patient);
    }
}