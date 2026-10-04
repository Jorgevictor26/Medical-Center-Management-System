<?php

namespace App\Repositories;

use App\Models\Procedure;
use Illuminate\Database\Eloquent\Collection;

class ProcedureRepository
{
    public function all(): Collection
    {
        return Procedure::orderBy('name')->get();
    }

    public function find(int $id): Procedure
    {
        return Procedure::findOrFail($id);
    }

    public function create(array $data): Procedure
    {
        return Procedure::create($data);
    }

    public function update(Procedure $procedure, array $data): Procedure
    {
        $procedure->update($data);

        return $procedure->refresh();
    }

    public function delete(Procedure $procedure): void
    {
        $procedure->delete();
    }
}