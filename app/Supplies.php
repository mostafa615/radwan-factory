<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Supplies extends Model
{

    protected $table = "supplies";

    protected $fillable = [
        'name',
        'type' ,
        'store_id' ,
        'height',
        'width',
        'init_quantity',
        'quantity',
        'used' ,
        'description',
        'created_at','updated_at'
    ];

    public function SupplieType(){
        return $this->belongsTo(SupplieTypes::class, 'type');
    }
    public function Store(){
        return $this->belongsTo(Store::class, 'store_id');
    }


    public function MachineSupplies(){
        return $this->hasMany(MachineSupplie::class, 'supplie_id', 'id');
    }



}
