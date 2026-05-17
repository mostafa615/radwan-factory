<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MachineGroup extends Model
{
    //
    protected $table = "machine_groups";

    protected $fillable = [
        'machine_id', 'group_id', 'type','notes'
    ];

    public function Group(){
        return $this->belongsTo(Group::class, 'group_id');
    }

    public function Machine(){
        return $this->belongsTo(Machines::class, 'machine_id');
    }



}
