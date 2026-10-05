<?php

namespace App\Services;

use App\Repositories\ReportRepository;

class ReportService
{
    public function __construct(
        private ReportRepository $reportRepository
    ) {}

    public function generate(
        string $from,
        string $to
    ): array {
        $consultations = $this->reportRepository
            ->getConsultations($from, $to);

        $totalRevenue = 0;
        $totalPaid = 0;

        $patients = [];
        $doctorSummary = [];
        $procedureSummary = [];

        $details = $consultations->map(function ($consultation) use (
            &$totalRevenue,
            &$totalPaid,
            &$patients,
            &$doctorSummary,
            &$procedureSummary
        ) {
            /*
        |--------------------------------------------------------------------------
        | Consultation financial calculation
        |--------------------------------------------------------------------------
        */

            $total = $consultation->consultationProcedures->sum(
                fn($item) => $item->quantity * $item->unit_price
            );

            $paid = $consultation->payments->sum('amount');

            $outstanding = $total - $paid;

            $totalRevenue += $total;
            $totalPaid += $paid;

            /*
        |--------------------------------------------------------------------------
        | Unique patients
        |--------------------------------------------------------------------------
        */

            $patients[$consultation->patient_id] = true;

            /*
        |--------------------------------------------------------------------------
        | Doctor summary
        |--------------------------------------------------------------------------
        */

            $doctorId = $consultation->doctor->id;

            if (!isset($doctorSummary[$doctorId])) {
                $doctorSummary[$doctorId] = [
                    'doctor_id' => $doctorId,
                    'doctor' => $consultation->doctor->name,
                    'patients' => [],
                    'consultations' => 0,
                    'revenue' => 0,
                ];
            }

            $doctorSummary[$doctorId]['patients'][$consultation->patient_id] = true;

            $doctorSummary[$doctorId]['consultations']++;

            $doctorSummary[$doctorId]['revenue'] += $total;

            /*
        |--------------------------------------------------------------------------
        | Procedure summary
        |--------------------------------------------------------------------------
        */

            foreach ($consultation->consultationProcedures as $item) {
                $procedureId = $item->procedure->id;

                if (!isset($procedureSummary[$procedureId])) {
                    $procedureSummary[$procedureId] = [
                        'procedure_id' => $procedureId,
                        'procedure' => $item->procedure->name,
                        'quantity' => 0,
                        'revenue' => 0,
                    ];
                }

                $procedureSummary[$procedureId]['quantity'] += $item->quantity;

                $procedureSummary[$procedureId]['revenue'] +=
                    $item->quantity * $item->unit_price;
            }

            /*
        |--------------------------------------------------------------------------
        | Detailed consultation
        |--------------------------------------------------------------------------
        */

            return [
                'consultation_id' => $consultation->id,

                'patient' => [
                    'id' => $consultation->patient->id,
                    'name' => $consultation->patient->name,
                    'id_number' => $consultation->patient->id_number,
                ],

                'doctor' => [
                    'id' => $consultation->doctor->id,
                    'name' => $consultation->doctor->name,
                    'specialization' => $consultation->doctor->specialization,
                ],

                'date' => $consultation->consultation_date,

                'status' => $consultation->status,

                'procedures' => $consultation->consultationProcedures
                    ->map(function ($item) {
                        return [
                            'name' => $item->procedure->name,
                            'quantity' => $item->quantity,
                            'unit_price' => $item->unit_price,
                            'total' => $item->quantity * $item->unit_price,
                            'tooth' => $item->tooth,
                            'observation' => $item->observation,
                        ];
                    })
                    ->values(),

                'total' => $total,
                'paid' => $paid,
                'outstanding' => $outstanding,

                'payment_status' => match (true) {
                    $paid <= 0 => 'unpaid',
                    $outstanding > 0 => 'partially_paid',
                    default => 'paid',
                },

                'payments' => $consultation->payments
                    ->map(function ($payment) {
                        return [
                            'amount' => $payment->amount,
                            'payment_method' => $payment->payment_method,
                            'payment_date' => $payment->payment_date,
                            'notes' => $payment->notes,
                        ];
                    })
                    ->values(),
            ];
        });

        /*
    |--------------------------------------------------------------------------
    | Convert doctor patient lists into patient counts
    |--------------------------------------------------------------------------
    */

        $doctorSummary = array_values(
            array_map(function ($doctor) {
                $doctor['patients'] = count($doctor['patients']);

                return $doctor;
            }, $doctorSummary)
        );

        /*
    |--------------------------------------------------------------------------
    | Final report
    |--------------------------------------------------------------------------
    */

        return [
            'period' => [
                'from' => $from,
                'to' => $to,
            ],

            'summary' => [
                'patients' => count($patients),
                'consultations' => $consultations->count(),

                'completed' => $consultations
                    ->where('status', 'completed')
                    ->count(),

                'cancelled' => $consultations
                    ->where('status', 'cancelled')
                    ->count(),

                'waiting' => $consultations
                    ->where('status', 'waiting')
                    ->count(),

                'in_progress' => $consultations
                    ->where('status', 'in_progress')
                    ->count(),

                'revenue' => $totalRevenue,
                'paid' => $totalPaid,
                'outstanding' => $totalRevenue - $totalPaid,
            ],

            'doctor_summary' => $doctorSummary,

            'procedure_summary' => array_values($procedureSummary),

            'consultations' => $details->values(),
        ];
    }
}
