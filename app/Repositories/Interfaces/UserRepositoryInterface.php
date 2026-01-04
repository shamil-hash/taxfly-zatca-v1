<?php

namespace App\Repositories\Interfaces;

interface UserRepositoryInterface
{
    // public function getUserDetails($userId); 
    // public function getBranchName($userId);
    // public function getShopData($adminId);

    public function getUserRoles($userId);
    public function getAdminId($userId);
}
