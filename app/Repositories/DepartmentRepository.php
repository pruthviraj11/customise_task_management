<?php
namespace App\Repositories;

use App\Models\Department;

class DepartmentRepository
{
    public function find($id)
    {
        return Department::find($id);
    }

    public function create(array $data)
    {
        return Department::create($data);
    }

    public function update($id, array $data)
    {
        return Department::findOrFail($id)->update($data);
    }

    public function delete($id)
    {
        $data = Department::where('id', $id)->first();

        return $data->delete();
    }
    public function getAll()
    {
        return Department::query();
    }
}
