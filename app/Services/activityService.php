<?php

namespace App\Services;

use App\Models\Activity;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class activityService
{

    public function __construct($id, $ip, $uri, $message, $user_type, $locationdata)
    {

        $this->id = $id;
        $this->ip = $ip;
        $this->uri = $uri;
        $this->message = $message;
        $this->user_type = $user_type;
        $this->locationdata = $locationdata;
    }




    public function ipaddress_store($branch_id)
    {

        if ($this->user_type == 'webadmin') {

            $ipaddress = new Activity();
            $ipaddress->admin_id = $this->id;
            $ipaddress->ipaddress = $this->ip;
            $ipaddress->is_admin = 1;
            $ipaddress->url = $this->uri;
            $ipaddress->message = $this->message;
            $ipaddress->countryName = $this->locationdata->countryName;
            $ipaddress->regionName = $this->locationdata->regionName;
            $ipaddress->cityName = $this->locationdata->cityName;
            $ipaddress->save();
        } elseif ($this->user_type == 'websoftware') {

            $ipaddress = new Activity();
            $ipaddress->user_id = $this->id;
            $ipaddress->branch_id = $branch_id;
            $ipaddress->ipaddress = $this->ip;
            $ipaddress->is_user = 1;
            $ipaddress->url = $this->uri;
            $ipaddress->message = $this->message;
            $ipaddress->countryName = $this->locationdata->countryName;
            $ipaddress->regionName = $this->locationdata->regionName;
            $ipaddress->cityName = $this->locationdata->cityName;
            $ipaddress->save();
        } elseif ($this->user_type == 'webcredituser') {

            $ipaddress = new Activity();
            $ipaddress->credituser_id = $this->id;
            $ipaddress->branch_id = $branch_id;
            $ipaddress->ipaddress = $this->ip;
            $ipaddress->is_credituser = 1;
            $ipaddress->url = $this->uri;
            $ipaddress->message = $this->message;
            $ipaddress->countryName = $this->locationdata->countryName;
            $ipaddress->regionName = $this->locationdata->regionName;
            $ipaddress->cityName = $this->locationdata->cityName;
            $ipaddress->save();
        }
    }
}
