<?php

namespace App\Services;

use Stevebauman\Location\Facades\Location;

class otherService
{

    public function __construct()
    {
    }

    public function get_location($ip)
    {
        $loc = Location::get($ip);
        // $loc = Location::get('103.179.196.204');

        return $loc;
    }
}
