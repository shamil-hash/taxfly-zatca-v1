<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bankhistory extends Model
{

    use HasFactory;
    protected $table='bank_history';
    protected $fillable=[
        'transaction_id',
        'reciept_no',
        'bank_id',
        'account_name',
        	'user_id',
            	'branch',
                	'detail',
                    	'amount',
                        'party',
                        'dr_cr',
                        'ref_no',
                        'remark',
                        'date'


        		];

}
