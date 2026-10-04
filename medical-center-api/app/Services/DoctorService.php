<?php

namespace App\Services;

use App\Models\Doctor;
use App\Repositories\DoctorRepository;
use Illuminate\Database\Eloquent\Collection;

class DoctorService
{
    public function __construct(
        private DoctorRepository $doctorRepository
    ) {}

    public function getAll(): Collection
    {
        return $this->doctorRepository->all();
    }

    public function getById(int $id): Doctor
    {
        return $this->doctorRepository->find($id);
    }

    public function create(array $data): Doctor
    {
        return $this->doctorRepository->create($data);
    }

    public function update(Doctor $doctor, array $data): Doctor
    {
        return $this->doctorRepository->update($doctor, $data);
    }

    public function delete(Doctor $doctor): void
    {
        $this->doctorRepository->delete($doctor);
    }
}