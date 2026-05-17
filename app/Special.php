<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Special extends Model
{

    protected $table = "items";

    protected $fillable = [
        'code', 
        'price', 
        'name', 
        'length', 
        'width', 
        'weight', 
        'notes', 
        'group_id',
        'is_damage', 
        'is_special',
        'operat_ord_id'
    ];


    public function Group(){
        return $this->belongsTo(Group::class, 'group_id');
    }

    public function quantities()
    {
        return $this->hasMany(Quantity::class)->with('item');
    }

}
