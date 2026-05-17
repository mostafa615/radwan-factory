<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OperationOrder extends Model
{

    protected $table = "operation_orders";

    protected $fillable = [
        'out_operation',
        'client_name',
        'out_confirmed',
        'confirm_user_id',
        'confirmed_at',
        'related_operat_ord_id',
        'machine_type_id',
        'machine_id',
        'employee_id',
        'user_id',
        'supervisor_id',
        'supervisor_store',
        'supervisor_process',
        'date',
        'total_used_length',
        'confirm_notes',
        'item_id',
        'old_item_quantity',
        'old_item_unit',
        'out_item_unit',
        'out_item_id',
        'operation_suplies_id',
        'out_group_id',
        'out_name',
        'price',
        'length',
        'width',
        'quantity',
        'is_special',
        'store_id',
        'notes',
        'created_by',
        'notes2',
        'store_employees',
        'is_complete',
        'machine_access',
        'is_deleted',
        'machine_edit',
        'store_edit',
    ];

    public function operationOrderDetails(){
        return $this->hasMany(OperationOrderDetail::class);
    }

    public function operationOrderResults(){
        return $this->hasMany(OperationOrderResult::class);
    }

    public function operationOrderResultDetails(){
        return $this->hasMany(OperationOrderResultDetail::class);
    }

    public function machine(){
        return $this->belongsTo(Machines::class, 'machine_id');
    }

    public function employee(){
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function user(){
        return $this->belongsTo(User::class, 'user_id');
    }

    public function supervisor(){
        return $this->belongsTo(Employee::class, 'supervisor_id');
    }
    
    public function item(){
        return $this->belongsTo(Item::class, 'item_id');
    }

    public function store(){
        return $this->belongsTo(Store::class, 'store_id');
    }
    
    public function oberationSupply(){
        return $this->belongsTo(Supplies::class, 'operation_suplies_id');
    }

    public function group(){
        return $this->belongsTo(Group::class, 'out_group_id');
    }
    
    public function store_employees(){
        return $this->belongsTo(Employee::class, 'store_employees');
    }

    public static function toUser(){
        $query = OperationOrder::query();
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
        return $query->whereIn('id', $myquery);
    }
}