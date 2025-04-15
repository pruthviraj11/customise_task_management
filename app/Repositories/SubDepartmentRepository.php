<?php
namespace App\Repositories;

use App\Models\SubDepartment;

class SubDepartmentRepository
{
    public function find($id)
    {
        return SubDepartment::find($id);
    }

    public function create(array $data)
    {
        
        return SubDepartment::create($data);
    }

    public function update($id, array $data)
    {
        return SubDepartment::findOrFail($id)->update($data);
    }

    public function delete($id)
    {
        // SubDepartment::Where('id', $id)->update($id, ['deleted_by' => auth()->user()->id]);
        return SubDepartment::findOrFail($id)->delete();
    }
    public function getAll()
    {
        return SubDepartment::query();
    }
}
