<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OperationOrderDetail extends Model
{

    protected $table = "operation_order_details";

    protected $fillable = [
        'operation_order_id',
        'group_id',
        'item_id',
        'item_name',
        'old_in_balance',
        'new_in_balance',
        'old_item_quantity',
        'out_item_id',
        'out_item_name',
        'old_out_balance',
        'new_out_balance',
        'operation_suplies_id',
        'out_group_id',
        'out_name',
        'price',
        'length',
        'width',
        'quantity',
        'is_special',
        'old_item_supp_quantity',
        'supplie_quantity_used',
        'supplie_quantity_pre_used',
        'active'
    ];

    public function operationOrder(){
        return $this->belongsTo(OperationOrder::class, 'operation_order_id');
    }

    public function operationOrderResults()
    {
        return $this->hasMany(OperationOrderResult::class, 'order_details_id', 'id');
    }

    public function item(){
        return $this->belongsTo(Item::class, 'item_id');
    }

    public function outItem(){
        return $this->belongsTo(Item::class, 'out_item_id');
    }

    public function oberationSupply(){
        return $this->belongsTo(Supplies::class, 'operation_suplies_id');
    }

    public function group(){
        return $this->belongsTo(Group::class, 'out_group_id');
    }

    public function inGroup() {
        return $this->belongsTo(Group::class, 'group_id');
    }
}
