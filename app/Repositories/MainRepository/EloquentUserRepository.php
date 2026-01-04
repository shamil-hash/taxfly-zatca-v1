<?php

namespace App\Repositories\MainRepository;

use App\Models\Softwareuser;
use App\Repositories\Interfaces\UserRepositoryInterface;
use Illuminate\Support\Facades\DB;

class EloquentUserRepository implements UserRepositoryInterface {

    public function getUserRoles($userId)
    {
        return DB::table('softwareusers')
            ->leftJoin('user_roles', 'softwareusers.id', '=', 'user_roles.user_id')
            ->where('user_id', $userId)
            ->get();
    }

    public function getAdminId($userId)
    {
        return Softwareuser::where('id', $userId)
            ->pluck('admin_id')
            ->first();
    }
}

