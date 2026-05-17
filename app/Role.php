<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use Zizaco\Entrust\EntrustRole;

class Role extends EntrustRole
{
    //
    /**
    * The attributes that aren't mass assignable.
    *
    * @var array
    */
    protected $guarded = [];

}
