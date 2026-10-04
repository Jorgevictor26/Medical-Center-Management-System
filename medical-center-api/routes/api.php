<?php

use App\Http\Controllers\Api\PatientController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\DoctorController;
use App\Http\Controllers\Api\ProcedureController;
use App\Http\Controllers\Api\ConsultationController;

Route::apiResource(
    'consultations',
    ConsultationController::class
);
Route::post(
    'consultations/{consultation}/procedures',
    [ConsultationController::class, 'addProcedure']
);
Route::apiResource('procedures', ProcedureController::class);
Route::apiResource('doctors', DoctorController::class);
Route::apiResource('patients', PatientController::class);
