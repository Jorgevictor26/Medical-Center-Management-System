<?php

namespace App\Services;

use App\Models\Procedure;
use App\Repositories\ProcedureRepository;
use Illuminate\Database\Eloquent\Collection;

class ProcedureService
{
    public function __construct(
        private ProcedureRepository $procedureRepository
    ) {}

    public function getAll(): Collection
    {
        return $this->procedureRepository->all();
    }

    public function getById(int $id): Procedure
    {
        return $this->procedureRepository->find($id);
    }

    public function create(array $data): Procedure
    {
        return $this->procedureRepository->create($data);
    }

    public function update(
        Procedure $procedure,
        array $data
    ): Procedure {
        return $this->procedureRepository->update($procedure, $data);
    }

    public function delete(Procedure $procedure): void
    {
        $this->procedureRepository->delete($procedure);
    }
}
