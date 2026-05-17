<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SuppliesExchanges extends Model
{
    // protected $table = ' supplies_exchanges';
    protected $fillable = ['date', 'old_machine_id', 'new_machine_id', 'supplie_id', 'transferred_quantity', 'old_machine_pre_used', 'new_machine_pre_used', 'old_machine_used', 'new_machine_used', 'notes'];
    protected $hidden = ['created_at', 'updated_at'];


    public function old_machine()
    {
        return $this->belongsTo(Machines::class, 'old_machine_id');
    }
    public function new_machine()
    {
        return $this->belongsTo(Machines::class, 'new_machine_id');
    }
    public function supplies()
    {
        return $this->belongsTo(Supplies::class, 'supplie_id');
    }
}
