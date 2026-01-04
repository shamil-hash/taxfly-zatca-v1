<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Adminuser extends Authenticatable
{
    protected $guard = 'adminusers';


    use HasFactory;
    protected $fillable = [
        'id', 'name', 'username', 'password', 'email', 'phone', 'currency','tax', 'po_box', 'postal_code', 'cr_number', 'location', 'address', 'logo'
    ];

    protected $hidden = [
        'password', 'remember token',

    ];

    public function getAdminDetails($field)
    {
        return $this->{$field};
    }

    public function getAdminName()
    {
        return $this->getAdminDetails('name');
    }
    public function getCRNumber()
    {
        return $this->getAdminDetails('cr_number');
    }
    public function getPOBox()
    {
        return $this->getAdminDetails('po_box');
    }
    public function getPhone()
    {
        return $this->getAdminDetails('phone');
    }

    public function getEmail()
    {
        return $this->getAdminDetails('email');
    }

    public function getCurrency()
    {
        return $this->getAdminDetails('currency');
    }
    public function gettax()
    {
        return $this->getAdminDetails('currency');
    }
}
