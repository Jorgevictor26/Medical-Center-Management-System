<?php

use App\Http\Controllers\Api\PatientController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\DoctorController;
use App\Http\Controllers\Api\ProcedureController;


Route::apiResource('procedures', ProcedureController::class);
Route::apiResource('doctors', DoctorController::class);
Route::apiResource('patients', PatientController::class);