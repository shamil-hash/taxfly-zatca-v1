<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'mobile', 'email', 'address', 'location', 'adminuser', 'softwareuser',
    ];

    public function getSupplierDetails($field)
    {
        return $this->{$field};
    }

    public function getSuppTRNumber()
    {
        return $this->getSupplierDetails('trn_number');
    }
    public function getSuppMobile()
    {
        return $this->getSupplierDetails('mobile');
    }
    public function getSuppEmail()
    {
        return $this->getSupplierDetails('email');
    }
    public function getSuppAddress()
    {
        return $this->getSupplierDetails('address');
    }
}