<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OperationOrderTrack extends Model
{
    protected $table = 'operation_order_tracks';

    protected $fillable = [
        'operation_order_id',
        'step_name',
        'user_id',
        'status',
        'action_at',
        'notes',
    ];

    public function operationOrder()
    {
        return $this->belongsTo(OperationOrder::class, 'operation_order_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
