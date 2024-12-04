<?php
namespace App\Services;

use App\Repositories\DepartmentRepository;

class DepartmentService
{
    protected DepartmentRepository $departmentRepository;

    public function __construct(DepartmentRepository $departmentRepository)
    {
        $this->departmentRepository = $departmentRepository;
    }
    public function create($departmentData)
    {
        $department = $this->departmentRepository->create($departmentData);
        return $department;
    }
    public function getAllDepartment()
    {
        $departmentes = $this->departmentRepository->getAll();
        return $departmentes;
    }
    public function getDepartment($id)
    {
        $department = $this->departmentRepository->find($id);
        return $department;
    }
    public function deleteDepartment($id)
    {
        $deleted = $this->departmentRepository->delete($id);
        return $deleted;
    }
    public function updateDepartment($id, $departmentData)
    {
        $updated = $this->departmentRepository->update($id, $departmentData);
        return $updated;
    }

}
