<?php

namespace App\Http\Controllers\Dashboard;
use App\Http\Controllers\Controller;
use App\OperationOrder;

class NotificationController extends Controller
{
    public function inOperationOrders() {
        $query = OperationOrder::query()
            ->where('out_operation', 0)
            ->whereHas('operationOrderDetails', function ($q) {
                $q->whereDoesntHave('operationOrderResults');
            });
    
        if(auth()->user()->hasRole('branch_factor_respons') || auth()->user()->hasRole('safe_factor_response') || auth()->user()->hasRole('factor_response')) {
            $query->where('supervisor_process', auth()->user()->id);
        }
        else if(auth()->user()->hasRole('store_factor_response')) {
            $query->where('supervisor_store', auth()->user()->id)->where('machine_access', 0);
        }
        else if(auth()->user()->hasRole('machine_response')) {
            $query->where('user_id', auth()->user()->id);
        }

        $operationOrdersCount = $query->count();

        return $operationOrdersCount;
    }

    public function outOperationOrders() {
        $query = OperationOrder::query()
            ->where('out_operation', 1)
            ->where('is_complete', 1)
            ->where('date', '>', '2023-12-02')
            ->whereHas('operationOrderDetails', function ($q) {
                $q->whereDoesntHave('operationOrderResults');
            });

        if(auth()->user()->hasRole('branch_factor_respons') || auth()->user()->hasRole('safe_factor_response') || auth()->user()->hasRole('factor_response')) {
            $query->where('supervisor_process', auth()->user()->id);
        }
        else if(auth()->user()->hasRole('store_factor_response')) {
            $query->where('supervisor_store', auth()->user()->id);
        }
        else if(auth()->user()->hasRole('machine_response')) {
            $query->where('user_id', auth()->user()->id);
        }

        $operationOrdersCount = $query->count();

        return $operationOrdersCount;
    }
}