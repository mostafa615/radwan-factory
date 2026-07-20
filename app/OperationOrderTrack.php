<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OperationOrderTrack extends Model
{
    protected $table = 'operation_order_tracks';

    protected $fillable = [
        'step_name',
        'user_id',
        'status',
        'action_at',
        'operation_order_id',
        'operation_order_detail_id',
        'notes',
    ];

    public function operationOrder()
    {
        return $this->belongsTo(OperationOrder::class, 'operation_order_id');
    }

    public function operationOrderDetail()
    {
        return $this->belongsTo(OperationOrderDetail::class, 'operation_order_detail_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
