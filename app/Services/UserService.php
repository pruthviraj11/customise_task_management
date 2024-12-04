<?php
namespace App\Services;
use App\Repositories\UserRepository;

class UserService

{
    protected UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }
    public function create($userData)
    {
        $user = $this->userRepository->create($userData);
        return $user;
    }
    public function getAllUser()
    {
        $useres = $this->userRepository->getAll();
        return $useres;
    }
    public function getUser($id)
    {
        $user = $this->userRepository->find($id);
        return $user;
    }
    public function deleteUser($id)
    {
        $deleted = $this->userRepository->delete($id);
        
        return $deleted;
    }
    public function updateUser($id,$userData)
    {
        $updated = $this->userRepository->update($id,$userData);
        return $updated;
    }

}
