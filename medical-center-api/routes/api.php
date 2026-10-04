<?php

use App\Http\Controllers\Api\PatientController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\DoctorController;

Route::apiResource('doctors', DoctorController::class);
Route::apiResource('patients', PatientController::class);