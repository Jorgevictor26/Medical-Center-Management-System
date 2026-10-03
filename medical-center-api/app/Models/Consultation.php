<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Consultation extends Model
{
    protected $fillable = [
        'patient_id',
        'doctor_id',
        'consultation_date',
        'observation',
        'status',
    ];

    protected $casts = [
        'consultation_date' => 'datetime',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }

    public function consultationProcedures(): HasMany
    {
        return $this->hasMany(ConsultationProcedure::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }
}