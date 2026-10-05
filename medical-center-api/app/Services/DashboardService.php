<?php

namespace App\Services;

use App\Repositories\DashboardRepository;

class DashboardService
{
    public function __construct(
        private DashboardRepository $dashboardRepository
    ) {}

    public function getDashboard(): array
    {
        $revenue = $this->dashboardRepository->getTodayRevenue();
        $paid = $this->dashboardRepository->getTodayPaid();

        return [
            'date' => today()->toDateString(),

            'summary' => [
                'patients' => $this->dashboardRepository->getPatients(),

                'consultations_today' =>
                $this->dashboardRepository->getTodayConsultations(),

                'completed_today' =>
                $this->dashboardRepository->getTodayCompleted(),

                'waiting' =>
                $this->dashboardRepository->getWaiting(),
                'revenue_today' => (float) $revenue,
                'paid_today' => (float) $paid,
                'outstanding_today' => (float) ($revenue - $paid),
            ],

            'waiting_patients' =>
            $this->dashboardRepository->getWaitingPatients(),
        ];
    }
}
