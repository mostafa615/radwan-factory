<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SupplieTypes extends Model
{

    protected $table = "supplie_types";

    protected $fillable = [
        'name','description', 'created_at', 'updated_at'
    ];



}
