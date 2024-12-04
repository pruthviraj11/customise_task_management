<?php
namespace App\Repositories;
use App\Models\Role;

class RoleRepository
{
    public function find($id)
    {
        return Role::find($id);
    }


    public function create(array $data,array $permissions)
    {
        $role = Role::create($data);
        $role->syncPermissions($permissions);
        return $role;
    }

    public function update($id, array $data,array $permissions)
    {
        $role = Role::find($id);
        $role->syncPermissions($permissions);
        return Role::where('id', $id)->update($data);
    }

    public function delete($id)
    {
        return Role::where('id', $id)->delete();
    }
    public function getAll(){
        return Role::get();
    }
}
