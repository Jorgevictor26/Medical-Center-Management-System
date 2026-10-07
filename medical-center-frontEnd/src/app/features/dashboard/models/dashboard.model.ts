export interface DashboardSummary {
    patients: number;
    consultations_today: number;
    completed_today: number;
    waiting: number;
    revenue_today: number;
    paid_today: number;
    outstanding_today: number;
}

export interface WaitingPatient {
    id: number;
    patient_id: number;
    doctor_id: number;
    consultation_date: string;
    observation: string | null;
    status: string;

    patient: {
        id: number;
        name: string;
        id_number: string;
    };

    doctor: {
        id: number;
        name: string;
        specialization: string;
    };
}

export interface Dashboard {
    date: string;
    summary: DashboardSummary;
    waiting_patients: WaitingPatient[];
}