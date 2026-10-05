<?php

use App\Http\Controllers\Api\PatientController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\DoctorController;
use App\Http\Controllers\Api\ProcedureController;
use App\Http\Controllers\Api\ConsultationController;
use App\Http\Controllers\Api\PaymentController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\DashboardController;



Route::get('dashboard', [DashboardController::class, 'index']);
Route::get('patients/{patient}/history', [PatientController::class, 'history']);
Route::apiResource('consultations', ConsultationController::class);
Route::apiResource('procedures', ProcedureController::class);
Route::apiResource('doctors', DoctorController::class);
Route::apiResource('patients', PatientController::class);
Route::get('reports', [ReportController::class, 'index']);
Route::apiResource(
    'payments',
    PaymentController::class
)->only([
    'index',
    'store',
    'show',
    'destroy'
]);
Route::patch(
    'consultations/{consultation}/status',
    [ConsultationController::class, 'updateStatus']
);
Route::get(
    'consultations/{consultation}/financial-summary',
    [ConsultationController::class, 'financialSummary']
);
Route::get(
    'consultations/waiting',
    [ConsultationController::class, 'waiting']
);
Route::apiResource(
    'consultations',
    ConsultationController::class
);
Route::post(
    'consultations/{consultation}/procedures',
    [ConsultationController::class, 'addProcedure']
);
