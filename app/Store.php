<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use \Illuminate\Database\Eloquent\SoftDeletes;

class Store extends Model
{

    protected $table = "stores";

    protected $fillable = [
        'name',
        'code',
        'phone_1',
        'phone_2',
        'phone_3',
        'address',
        'type',
        'user_id',
        'country_id ',
    ];


}
