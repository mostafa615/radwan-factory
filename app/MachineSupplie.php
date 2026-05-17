<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MachineSupplie extends Model
{
    //
    protected $table = "machine_supplies";

    protected $fillable = [
        'supplie_id', 'machine_id','quantity', 'date', 'used', 'notes','transfer_quantity'
    ];

    public function supplie(){
        return $this->belongsTo(Supplies::class, 'supplie_id');
    }

    public function machine(){
        return $this->belongsTo(Machines::class, 'machine_id');
    }



}
