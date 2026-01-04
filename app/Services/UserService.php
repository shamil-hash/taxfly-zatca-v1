<?php

namespace App\Services;

use App\Repositories\Interfaces\UserRepositoryInterface;


class UserService
{
    protected $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function getUserDetails($userId)
    {
        return $this->userRepository->getUserRoles($userId);
    }

    public function getAdminId($userId)
    {
        return $this->userRepository->getAdminId($userId);
    }
}
