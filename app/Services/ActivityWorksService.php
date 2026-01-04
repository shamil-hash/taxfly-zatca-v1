<?php

namespace App\Services;

use App\Models\Activity;

class ActivityWorksService
{
    public function getActivities($branch)
    {

        if ($branch == null) {
            $datas = Activity::select('activities.*', 'adminusers.username as admin_name', 'softwareusers.username as user_name', 'creditusers.username as credituser_name', 'branches.branchname')
                ->leftJoin('adminusers', 'activities.admin_id', '=', 'adminusers.id')
                ->leftJoin('softwareusers', 'activities.user_id', '=', 'softwareusers.id')
                ->leftJoin('creditusers', 'activities.credituser_id', '=', 'creditusers.id')
                ->leftJoin('branches', 'activities.branch_id', '=', 'branches.id')
                ->orderBy('activities.created_at', 'DESC')
                ->groupBy('activities.created_at')
                ->get();
        } else {
            $datas = Activity::select('activities.*', 'adminusers.username as admin_name', 'softwareusers.username as user_name', 'creditusers.username as credituser_name', 'branches.branchname')
                ->leftJoin('adminusers', 'activities.admin_id', '=', 'adminusers.id')
                ->leftJoin('softwareusers', 'activities.user_id', '=', 'softwareusers.id')
                ->leftJoin('creditusers', 'activities.credituser_id', '=', 'creditusers.id')
                ->leftJoin('branches', 'activities.branch_id', '=', 'branches.id')
                ->where('activities.branch_id', $branch)
                ->orderBy('activities.created_at', 'DESC')
                ->groupBy('activities.created_at')
                ->get();
        }

        return $datas;
    }
}