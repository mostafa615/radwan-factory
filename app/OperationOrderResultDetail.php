<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OperationOrderResultDetail extends Model
{

    protected $table = "operation_order_result_details";

    protected $fillable = [
        'operation_order_id',
        'order_details_id',
        'order_results_id',
        'damage_type',
        'damage_name',
        'old_damage_id',
        'damage_quantity',
        'damage_length',
        'damage_width',
        'damage_thickness',
        'damage_price',
        'damage_weight',
    ];

    public function operationOrder(){
        return $this->belongsTo(OperationOrder::class, 'operation_order_id');
    }

    public function operationOrderDetail(){
        return $this->belongsTo(OperationOrderDetail::class, 'order_details_id');
    }

    public function operationOrderResult(){
        return $this->belongsTo(OperationOrderResult::class, 'order_results_id');
    }


}
