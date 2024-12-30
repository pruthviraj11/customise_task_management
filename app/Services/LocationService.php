<?php
namespace App\Services;

use App\Repositories\LocationRepository;

class LocationService
{
    protected LocationRepository $locationRepository;

    public function __construct(LocationRepository $locationRepository)
    {
        $this->locationRepository = $locationRepository;
    }
    public function create($locationData)
    {
        $location = $this->locationRepository->create($locationData);
        return $location;
    }
    public function getAllLocation()
    {
        $locations = $this->locationRepository->getAll();
        return $locations;
    }
    public function getLocation($id)
    {
        $location = $this->locationRepository->find($id);
        return $location;
    }
    public function deleteLocation($id)
    {
        $deleted = $this->locationRepository->delete($id);
        return $deleted;
    }
    public function updateLocation($id, $locationData)
    {
        $updated = $this->locationRepository->update($id, $locationData);
        return $updated;
    }

}
