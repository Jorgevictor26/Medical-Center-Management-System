<?php

namespace App\Repositories;

use App\Models\Consultation;
use Illuminate\Database\Eloquent\Collection;

class ReportRepository
{
    public function getConsultations(
        string $from,
        string $to
    ): Collection {
        return Consultation::with([
            'patient',
            'doctor',
            'consultationProcedures.procedure',
            'payments',
        ])
            ->whereBetween('consultation_date', [
                $from . ' 00:00:00',
                $to . ' 23:59:59',
            ])
            ->orderBy('consultation_date')
            ->get();
    }
}
