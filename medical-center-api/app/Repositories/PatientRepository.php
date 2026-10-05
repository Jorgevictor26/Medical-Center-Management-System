<?php

namespace App\Repositories;

use App\Models\Patient;
use Illuminate\Database\Eloquent\Collection;

class PatientRepository
{
    public function all(): Collection
    {
        return Patient::orderBy('name')->get();
    }

    public function find(int $id): Patient
    {
        return Patient::findOrFail($id);
    }

    public function create(array $data): Patient
    {
        return Patient::create($data);
    }

    public function update(Patient $patient, array $data): Patient
    {
        $patient->update($data);

        return $patient->refresh();
    }

    public function delete(Patient $patient): void
    {
        $patient->delete();
    }
    public function getHistory(int $patientId): Patient
    {
        return Patient::with([
            'consultations' => function ($query) {
                $query->latest('consultation_date');
            },
            'consultations.doctor',
            'consultations.consultationProcedures.procedure',
            'consultations.payments',
        ])->findOrFail($patientId);
    }
}
