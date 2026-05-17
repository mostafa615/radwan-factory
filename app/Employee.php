<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
class Employee extends Model
{
    //
    protected $table = "employees";

    protected $appends = ['employee_performance', 'supervisor_performance'];

    public function getEmployeePerformanceAttribute()
    {
        $operationOrders= OperationOrder::where('machine_id', request()->machine_id)
                                    ->whereBetween('date',[request()->date_from, request()->date_to])
                                    ->get();
        $employeeTotalLength = 0;
        foreach ($operationOrders as $operationOrder) {
            $employeesIds = explode(',', $operationOrder->employee_id);
            if(in_array($this->id, $employeesIds)){
                if($operationOrder->out_operation == 1){
                    $outLength = $operationOrder->total_used_length;
                    $employeeTotalLength += $outLength;
                }
                $length = DB::table('operation_order_results')
                            ->where('operation_order_id', $operationOrder->id)
                            ->sum('total_used_length');
                $employeeTotalLength += $length;
            }
        }

        return number_format($employeeTotalLength, 2);
    }

    public function getSupervisorPerformanceAttribute()
    {
        $operationOrders= OperationOrder::where('machine_id', request()->machine_id)
                                    ->whereBetween('date',[request()->date_from, request()->date_to])
                                    ->get();
        $supervisorTotalLength = 0;
        foreach ($operationOrders as $operationOrder) {
            $employeesIds = explode(',', $operationOrder->supervisor_id);
            if(in_array($this->id, $employeesIds)){
                if($operationOrder->out_operation == 1){
                    $outLength = $operationOrder->total_used_length;
                    $supervisorTotalLength += $outLength;
                }
                $length = DB::table('operation_order_results')
                            ->where('operation_order_id', $operationOrder->id)
                            ->sum('total_used_length');
                $supervisorTotalLength += $length;
            }
        }

        return number_format($supervisorTotalLength, 2);
    }
}
