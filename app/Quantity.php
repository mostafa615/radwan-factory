<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Quantity extends Model
{

    protected $table = "quantities";

    protected $fillable = [
        'ownerable_id',
        'ownerable_type',
        'item_id',
        'quantity',
        'length',
        'width',
        'weight_one',
        'init'
    ];


    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id');
    }





}
