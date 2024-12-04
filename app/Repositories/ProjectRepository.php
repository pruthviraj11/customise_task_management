<?php
namespace App\Repositories;

use App\Models\Project;

class ProjectRepository
{
    public function find($id)
    {
        return Project::find($id);
    }

    public function create(array $data)
    {
        return Project::create($data);
    }

    public function update($id, array $data)
    {
        return Project::where('id', $id)->update($data);
    }

    public function delete($id)
    {
        Project::Where('id', $id)->update(['deleted_by' => auth()->user()->id]);
        return Project::where('id', $id)->delete();
    }
    public function getAll()
    {
        return Project::query();
    }
}
