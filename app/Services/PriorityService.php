<?php
namespace App\Services;

use App\Repositories\PriorityRepository;

class PriorityService
{
    protected PriorityRepository $priorityRepository;

    public function __construct(PriorityRepository $priorityRepository)
    {
        $this->priorityRepository = $priorityRepository;
    }
    public function create($PriorityData)
    {
        $Priority = $this->priorityRepository->create($PriorityData);
        return $Priority;
    }
    public function getAllpriority()
    {
        $Priorityes = $this->priorityRepository->getAll();
        return $Priorityes;
    }
    public function getpriority($id)
    {
        $Priority = $this->priorityRepository->find($id);
        return $Priority;
    }
    public function deletepriority($id)
    {
        $deleted = $this->priorityRepository->delete($id);
        return $deleted;
    }
    public function updatepriority($id, $PriorityData)
    {
        $updated = $this->priorityRepository->update($id, $PriorityData);
        return $updated;
    }

}
