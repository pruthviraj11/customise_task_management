<?php
namespace App\Services;
use App\Repositories\RoleRepository;

class RoleService

{
    protected $roleRepository;

    public function __construct(RoleRepository $roleRepository)
    {
        $this->roleRepository = $roleRepository;
    }
    public function create($roleData,$permissions)
    {
        $role = $this->roleRepository->create($roleData,$permissions);
        return $role;
    }

    public function getAllRoles()
    {
        $roles = $this->roleRepository->getAll();
        return $roles;
    }
    public function getRole($id)
    {
        $role = $this->roleRepository->find($id);
        return $role;
    }
    public function deleteRole($id)
    {
        $deleted = $this->roleRepository->delete($id);
        return $deleted;
    }
    public function updateRole($id,$roleData,$permissions)
    {
        $updated = $this->roleRepository->update($id,$roleData,$permissions);
        return $updated;
    }

}
