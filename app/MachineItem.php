<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MachineItem extends Model
{
    //
    protected $table = "machine_items";

    protected $fillable = [
        'machine_id', 'item_id', 'quantity', 'date','notes'
    ];

    public function Item(){
        return $this->belongsTo(Item::class, 'item_id');
    }

    public function Machine(){
        return $this->belongsTo(Machines::class, 'machine_id');
    }



}
