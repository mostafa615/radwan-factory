<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TrackingMachineSupplies extends Model
{
    // protected $table = 'tracking_machine_supplies';

    protected $fillable = ['id', 'machine_id', 'supplie_id', 'init_quantity', 'quantity', 'date', 'type', 'operation_order_id', 'exchange_id', 'machine_supplie_id', 'operation_order_result_id', 'last_quantity'];
    protected $hidden = ['created_at', 'updated_at'];



    public function machine()
    {
        return $this->belongsTo(Machines::class, 'machine_id');
    }
    public function supply()
    {
        return $this->belongsTo(Supplies::class, 'supplie_id');
    }
}
