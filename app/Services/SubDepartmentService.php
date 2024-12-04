<?php
namespace App\Services;

use App\Repositories\SubDepartmentRepository;

class SubDepartmentService
{
    protected SubDepartmentRepository $sub_departmentRepository;

    public function __construct(SubDepartmentRepository $sub_departmentRepository)
    {
        $this->sub_departmentRepository = $sub_departmentRepository;
    }
    public function create($sub_departmentData)
    {
        $sub_department = $this->sub_departmentRepository->create($sub_departmentData);
        return $sub_department;
    }
    public function getAllSubDepartment()
    {
        $sub_departmentes = $this->sub_departmentRepository->getAll();
        return $sub_departmentes;
    }
    public function getSubDepartment($id)
    {
        $sub_department = $this->sub_departmentRepository->find($id);
        return $sub_department;
    }
    public function deleteSubDepartment($id)
    {

        $deleted = $this->sub_departmentRepository->delete($id);
        return $deleted;
    }
    public function updateSubDepartment($id, $sub_departmentData)
    {
        $updated = $this->sub_departmentRepository->update($id, $sub_departmentData);
        return $updated;
    }

}
