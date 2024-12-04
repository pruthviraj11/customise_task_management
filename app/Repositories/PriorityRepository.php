<?php
namespace App\Repositories;

use App\Models\Priority;

class PriorityRepository
{
    public function find($id)
    {
        return Priority::find($id);
    }

    public function create(array $data)
    {
        return Priority::create($data);
    }

    public function update($id, array $data)
    {
        return Priority::findOrFail($id)->update($data);
    }

    public function delete($id)
    {
        Priority::Where('id', $id)->update(['deleted_by' => auth()->user()->id]);
        return Priority::findOrFail($id)->delete();
    }
    public function getAll()
    {
        return Priority::query();
    }
}
