<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MachineTypes extends Model
{

    protected $table = "machine_types";

    protected $fillable = [
        'name','description', 'created_at', 'updated_at'
    ];



}
