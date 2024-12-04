<?php
namespace App\Services;

use App\Repositories\ProjectStatusRepository;

class ProjectStatusService
{
    protected ProjectStatusRepository $projectStatusRepository;

    public function __construct(ProjectStatusRepository $projectStatusRepository)
    {
        $this->projectStatusRepository = $projectStatusRepository;
    }
    public function create($ProjectStatusData)
    {
        $ProjectStatus = $this->projectStatusRepository->create($ProjectStatusData);
        return $ProjectStatus;
    }
    public function getAllProjectStatus()
    {
        $ProjectStatuses = $this->projectStatusRepository->getAll();
        return $ProjectStatuses;
    }
    public function getProjectStatus($id)
    {
        $ProjectStatus = $this->projectStatusRepository->find($id);
        return $ProjectStatus;
    }
    public function deleteProjectStatus($id)
    {
        $deleted = $this->projectStatusRepository->delete($id);
        return $deleted;
    }
    public function updateProjectStatus($id, $ProjectStatusData)
    {
        $updated = $this->projectStatusRepository->update($id, $ProjectStatusData);
        return $updated;
    }

}
