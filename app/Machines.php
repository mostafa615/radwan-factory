<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Machines extends Model
{

    protected $table = "machines";

    protected $fillable = [
        'name',
        'type' ,
        'store_id' ,
        'description'
    ];

    public function MachineType(){
        return $this->belongsTo(MachineTypes::class, 'type');
    }

    public function Store(){
        return $this->belongsTo(Store::class, 'store_id');
    }



}
