<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Patient extends Model
{
    protected $fillable = [
        'full_name',
        'id_number',
        'date_of_birth',
        'gender',
        'phone',
        'address',
    ];

    public function consultations(): HasMany
    {
        return $this->hasMany(Consultation::class);
    }
}