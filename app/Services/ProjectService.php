<?php
namespace App\Services;

use App\Repositories\ProjectRepository;

class projectService
{
    protected projectRepository $projectRepository;

    public function __construct(projectRepository $projectRepository)
    {
        $this->projectRepository = $projectRepository;
    }
    public function create($projectData)
    {
        $project = $this->projectRepository->create($projectData);
        return $project;
    }
    public function getAllproject()
    {
        $projectes = $this->projectRepository->getAll();
        return $projectes;
    }
    public function getproject($id)
    {
        $project = $this->projectRepository->find($id);
        return $project;
    }
    public function deleteproject($id)
    {
        $this->projectRepository->update($id, ['deleted_by' => auth()->user()->id]);
        $deleted = $this->projectRepository->delete($id);
        return $deleted;
    }
    public function updateproject($id, $projectData)
    {
        $updated = $this->projectRepository->update($id, $projectData);
        return $updated;
    }

}
