<?php

namespace App\Repositories;

use App\Models\Consultation;
use App\Models\Patient;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;

class DashboardRepository
{
    public function getTodayConsultations()
    {
        return Consultation::whereDate('consultation_date', today())->count();
    }

    public function getTodayCompleted()
    {
        return Consultation::whereDate('consultation_date', today())
            ->where('status', 'completed')
            ->count();
    }

    public function getWaiting()
    {
        return Consultation::where('status', 'waiting')->count();
    }

    public function getPatients()
    {
        return Patient::count();
    }

    public function getTodayRevenue()
    {
        return DB::table('consultation_procedures')
            ->join(
                'consultations',
                'consultation_procedures.consultation_id',
                '=',
                'consultations.id'
            )
            ->whereDate('consultations.consultation_date', today())
            ->sum(
                DB::raw(
                    'consultation_procedures.quantity *
                     consultation_procedures.unit_price'
                )
            );
    }

    public function getTodayPaid()
    {
        return Payment::whereDate('payment_date', today())
            ->sum('amount');
    }

    public function getWaitingPatients()
    {
        return Consultation::with(['patient', 'doctor'])
            ->where('status', 'waiting')
            ->orderBy('consultation_date')
            ->get();
    }
}
