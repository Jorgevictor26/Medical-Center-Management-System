<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConsultationProcedure extends Model
{
    protected $fillable = [
        'consultation_id',
        'procedure_id',
        'quantity',
        'unit_price',
        'tooth',
        'observation',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
    ];

    public function consultation(): BelongsTo
    {
        return $this->belongsTo(Consultation::class);
    }

    public function procedure(): BelongsTo
    {
        return $this->belongsTo(Procedure::class);
    }
}