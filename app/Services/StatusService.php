<?php
namespace App\Services;

use App\Repositories\StatusRepository;

class StatusService
{
    protected StatusRepository $statusRepository;

    public function __construct(StatusRepository $statusRepository)
    {
        $this->statusRepository = $statusRepository;
    }
    public function create($statusData)
    {
        $status = $this->statusRepository->create($statusData);
        return $status;
    }
    public function getAllstatus()
    {
        $statuses = $this->statusRepository->getAll();
        return $statuses;
    }
    public function getstatus($id)
    {
        $status = $this->statusRepository->find($id);
        return $status;
    }
    public function deletestatus($id)
    {
        $deleted = $this->statusRepository->delete($id);
        return $deleted;
    }
    public function updatestatus($id, $statusData)
    {
        $updated = $this->statusRepository->update($id, $statusData);
        return $updated;
    }

}
