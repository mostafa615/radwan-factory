<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MachineSupplieTrack extends Model
{
    protected $table = 'machine_supplie_tracks';

    protected $fillable = [
        'date',
        'machine_id',
        'supplie_id',
        'quantity',
        'type',
        'notes',
    ];

    public function supplie()
    {
        return $this->belongsTo(Supplies::class, 'supplie_id');
    }

    public function machine()
    {
        return $this->belongsTo(Machines::class, 'machine_id');
    }
}
