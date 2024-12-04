<?php
namespace App\Services;

use App\Repositories\DashboardRepository;

class DashboardService
{
    protected DashboardRepository $dashboardRepository;

    public function __construct(DashboardRepository $dashboardRepository)
    {
        $this->dashboardRepository = $dashboardRepository;
    }
    public function create($dashboardData)
    {
        $dashboard = $this->dashboardRepository->create($dashboardData);
        return $dashboard;
    }
    public function getAllDashboard()
    {
        $dashboardes = $this->dashboardRepository->getAll();
        return $dashboardes;
    }
    public function getDashboard($id)
    {
        $dashboard = $this->dashboardRepository->find($id);
        return $dashboard;
    }
    public function deleteDashboard($id)
    {
        $deleted = $this->dashboardRepository->delete($id);
        return $deleted;
    }
    public function updateDashboard($id, $dashboardData)
    {
        $updated = $this->dashboardRepository->update($id, $dashboardData);
        return $updated;
    }

}
