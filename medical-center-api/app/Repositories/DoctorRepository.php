<?php

namespace App\Repositories;

use App\Models\Doctor;
use Illuminate\Database\Eloquent\Collection;

class DoctorRepository
{
    public function all(): Collection
    {
        return Doctor::orderBy('full_name')->get();
    }

    public function find(int $id): Doctor
    {
        return Doctor::findOrFail($id);
    }

    public function create(array $data): Doctor
    {
        return Doctor::create($data);
    }

    public function update(Doctor $doctor, array $data): Doctor
    {
        $doctor->update($data);

        return $doctor->refresh();
    }

    public function delete(Doctor $doctor): void
    {
        $doctor->delete();
    }
}