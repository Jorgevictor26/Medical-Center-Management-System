<?php

namespace App\Repositories;

use App\Models\Consultation;
use Illuminate\Database\Eloquent\Collection;

class ConsultationRepository
{
    public function all(): Collection
    {
        return Consultation::with(['patient', 'doctor'])
            ->latest('consultation_date')
            ->get();
    }

    public function find(int $id): Consultation
    {
        return Consultation::with([
            'patient',
            'doctor',
            'consultationProcedures.procedure',
            'payments'
        ])->findOrFail($id);
    }

    public function create(array $data): Consultation
    {
        return Consultation::create($data);
    }

    public function update(
        Consultation $consultation,
        array $data
    ): Consultation {
        $consultation->update($data);

        return $consultation->refresh();
    }

    public function delete(Consultation $consultation): void
    {
        $consultation->delete();
    }
    public function getWaiting(): Collection
    {
        return Consultation::with([
            'patient',
            'doctor'
        ])
            ->where('status', 'waiting')
            ->orderBy('consultation_date')
            ->get();
    }
}
