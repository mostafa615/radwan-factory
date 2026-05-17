<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OperationOrderResult extends Model
{

    protected $table = "operation_order_results";

    protected $fillable = [
        'operation_order_id',
        'order_details_id',
        'damage',
        'old_damage_id',
        'actual_output',
        'old_item_quantity',
        'total_used_length',
        'weight',
        'thickness',
        'damage_price',
        'confirmed',
        'confirm_notes',
        'user_id',
        'employee_id',
        'confirmed_at',
        'notes',
        
        'store_confirm',
        'store_confirm_notes',
        'store_employees',
        'is_deleted',
    ];

    public function operationOrder(){
        return $this->belongsTo(OperationOrder::class, 'operation_order_id');
    }

    public function operationOrderDetail(){
        return $this->belongsTo(OperationOrderDetail::class, 'order_details_id');
    }

    public function orderResultDetails()
    {
        return $this->hasMany(OperationOrderResultDetail::class, 'order_results_id', 'id');
    }

    public function user(){
        return $this->belongsTo(User::class, 'user_id');
    }
    
    public function store_employees(){
        return $this->belongsTo(Employee::class, 'store_employees');
    }

    public static function toUser(){
        $query = OperationOrderResult::query();
        $allOperationOrders = OperationOrder::all();
        $myquery= [];
        foreach ($allOperationOrders as $allOperationOrder) {
            $userIds = explode(',', $allOperationOrder->user_id);
            $user = User::whereIn('id', $userIds)
                                ->where('id', auth()->user()->id)
                                ->first();
            if($user){
                $myquery[] = $allOperationOrder->id;
            }
        }
        return $query->whereIn('operation_order_id', $myquery);
    }
}