<?php
namespace App\Repositories;

use App\Models\ProjectStatus;

class ProjectStatusRepository
{
    public function find($id)
    {
        return ProjectStatus::find($id);
    }

    public function create(array $data)
    {
        return ProjectStatus::create($data);
    }

    public function update($id, array $data)
    {
        return ProjectStatus::findOrFail($id)->update($data);
    }

    public function delete($id)
    {
        ProjectStatus::Where('id', $id)->update(['deleted_by' => auth()->user()->id]);
        return ProjectStatus::findOrFail($id)->delete();
    }
    public function getAll()
    {
        return ProjectStatus::query();
    }
}
