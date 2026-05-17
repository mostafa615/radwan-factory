<?php

namespace App\Http\Controllers\Dashboard;

use App\Department;
use App\Student;
use App\Admin;
use App\Reposite;
use App\SupplieTypes;
use App\MachineSupplie;
use App\OperationOrder;
use App\OperationOrderDetail;
use App\OperationOrderResult;
use App\OperationOrderResultDetail;
use App\Item;
use App\Damage;
use App\Employee;
use App\Special;
use App\Supplies;
use App\Quantity;
use App\Machines; 
use App\Store;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\SuppliesExchanges;
use App\TrackingMachineSupplies;
use App\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables as FacadesDataTables;
use Storage;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class ReportController extends Controller
{

    public function index(Request $request)
    {
        return view('dashboard.reports.index');
    }

    public function machine_report(Request $request)
    {
        $this->validate($request, [
            'machine_id' => 'required',
            'date_from' => 'required',
            'date_to' => 'required',
        ]);
        $operation_orders= OperationOrder::where('machine_id', $request->machine_id)
                                    ->whereBetween('date',[$request->date_from, $request->date_to])
                                    ->with('operationOrderDetails','operationOrderResults','operationOrderResultDetails')
                                    ->get();
        return view('dashboard.reports.machine_report', compact('operation_orders'));
    }

    public function machineSuppliesReport(Request $request)
    {
        $this->validate($request, [
            'supplie_id' => 'required',
            'date_from' => 'required',
            'date_to' => 'required',
        ]);

        $requestedFrom = Carbon::parse($request->date_from);
        $minStartDate = Carbon::create(2026, 5, 13);
        $startDate = $requestedFrom->lessThan($minStartDate) ? $minStartDate : $requestedFrom;
        $endDate = Carbon::parse($request->date_to);

        if ($startDate->gt($requestedFrom)) {
            $request->merge(['date_from' => $startDate->format('Y-m-d')]);
        }

        $machineSupplies = MachineSupplie::where('supplie_id', $request->supplie_id)
            ->whereBetween('date', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->with('Supplie', 'Machine')
            ->orderBy('created_at', 'asc')
            ->get();

        $spplie = Supplies::find($request->supplie_id);
        $snapshot = DB::table('supply_snapshots')
            ->where('supplie_id', $request->supplie_id)
            ->orderBy('snapshot_date', 'desc')
            ->first();
        if ($spplie && $snapshot) {
            $spplie->init_quantity = $snapshot->quantity;
        }

        $supplyOrders = DB::table('order_details')
            ->where('is_oper_supplies', 1)
            ->where('item_id', $request->supplie_id)
            ->whereBetween('created_at', [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')])
            ->orderBy('created_at', 'asc')
            ->get();

        $openingBalance = $spplie ? ($snapshot->quantity ?? $spplie->init_quantity) : 0;
        $usedQuantity = $machineSupplies->sum('quantity');
        $supplyOrdersTotal = $supplyOrders->sum(function ($order) {
            return $order->role == 'return-out' ? -$order->quantity : $order->quantity;
        });
        $currentBalance = $openingBalance - $usedQuantity + $supplyOrdersTotal;

        return view(
            'dashboard.reports.machine_supplies',
            compact('machineSupplies', 'spplie', 'supplyOrders', 'request', 'openingBalance', 'currentBalance')
        );
    }



    public function machineSupplieInventory(Request $request)
    {
        $this->validate($request, [
            'machine_id' => 'required',
            // 'date_from' => 'required',
            // 'date_to' => 'required',
        ]);
        $machineSupplies= MachineSupplie::where('machine_id', $request->machine_id)
                                    // ->whereBetween('date',[$request->date_from, $request->date_to])
                                    ->where('used' , '>', 0)
                                    ->with('supplie','machine')
                                    ->get();

        return view('dashboard.reports.machine_supplies_inventory', compact('machineSupplies','request'));
    }

    public function suppliesReport(Request $request)
    {
        $supplies =  Supplies::latest()->get();

        return view('dashboard.reports.supplies_report', compact('supplies'));
    }

    public function operationOrderResults(Request $request)
    {
        $this->validate($request, [
            'date_from' => 'required',
            'date_to' => 'required',
        ]);
        $operationOrderResults= OperationOrderResult::
                                    whereBetween('created_at',[$request->date_from, $request->date_to])
                                    ->latest()->get();
        // dd($machineSupplies);
        return view('dashboard.reports.operation_order_result', compact('operationOrderResults'));
    }

    public function employeesPerformance(Request $request)
    {
        $this->validate($request, [
            'machine_id' => 'required',
            'date_from' => 'required',
            'date_to' => 'required',
        ]);

        $employees = Employee::latest()->get();

        $operationOrdersIds = OperationOrder::where('machine_id', $request->machine_id)
                                            ->where('out_operation', 0)
                                            ->whereBetween('date',[$request->date_from, $request->date_to])
                                            ->pluck('id')->toArray();

        $outOperationOrders = OperationOrder::where('machine_id', $request->machine_id)
                                            ->where('out_operation', 1)
                                            ->whereBetween('date',[$request->date_from, $request->date_to])
                                            ->latest()->get();

        $operationOrderResults= OperationOrderResult::
                                    whereIn('operation_order_id',$operationOrdersIds)
                                    ->latest()->get();

        // dd($employees->employee_performance);
        return view('dashboard.reports.employees_performance', compact('employees', 'operationOrderResults', 'outOperationOrders'));
    }

    public function confirmNotesReport(Request $request)
    {
        $this->validate($request, [
            'date_from' => 'required',
            'date_to' => 'required',
        ]);
        $outOperationOrders = OperationOrder::where('out_operation', 1)
                                    ->whereNotNull('confirm_notes')
                                    ->whereBetween('date',[$request->date_from, $request->date_to])
                                    ->get();

        $operationOrderResults = OperationOrderResult::whereNotNull('confirm_notes')
                                    ->whereBetween('created_at',[$request->date_from, $request->date_to])
                                    ->latest()->get();
        // dd($operationOrderResults);
        return view('dashboard.reports.confirm_notes', compact('outOperationOrders', 'operationOrderResults'));
    }

    



    public function machine_supplie_used_inventory(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'machine_id' => 'required',
            'supplie_id' => 'required',
            'date_from' => 'required',
            'date_to' => 'required'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator->errors())->withInput($request->all());
        }
        $from = Carbon::parse($request['date_from']);
        $to = Carbon::parse($request['date_to']);
        $total_used = [];
        $machine_supplie = MachineSupplie::find($request['supplie_id']);




        /**
         * Exchange Out & Exchange In
         */
        // $exchange_query = SuppliesExchanges::query()
        //     ->where('supplie_id', $machine_supplie->supplie_id)
        //     ->whereBetween('date', [$from, $to]);

        // $exchange_from = clone $exchange_query;
        // $exchange_from = $exchange_from->where('old_machine_id', $machine_supplie->machine_id)->get();
        // $exchange_to = clone $exchange_query;
        // $exchange_to = $exchange_to->where('new_machine_id', $machine_supplie->machine_id)->get();
        /**
         * Operation Order In & Operation Order Out
         */
        $query = TrackingMachineSupplies::query()
            ->whereBetween('date', [$from, $to])
            ->where('supplie_id', $machine_supplie->supplie_id)
            ->where('machine_id', $request['machine_id'])
            ->orderBy('date');

        $dates = TrackingMachineSupplies::query()
            ->whereBetween('date', [$from, $to])
            ->where('supplie_id', $machine_supplie->supplie_id)
            ->where('machine_id', $request['machine_id'])
            ->orderBy('date')->groupBy('date')->pluck('date')->toArray();


        $dates_array = TrackingMachineSupplies::query()
            ->where('supplie_id', $machine_supplie->supplie_id)
            ->where('machine_id', $request['machine_id'])
            ->orderBy('date')->groupBy('date')->pluck('date')->toArray();


        // return $dates;
        $operation_orders_in = clone $query;
        $operation_orders_in = $operation_orders_in->where('type', 'operation_in')->get();
        $operation_orders_out = clone $query;
        $operation_orders_out = $operation_orders_out->where('type', 'operation_out')->get();
        $machine_supplies = clone $query;
        $machine_supplies = $machine_supplies->where('type', 'machine_supplie')->get();
        $exchange_from = clone $query;
        $exchange_from = $exchange_from->where('type', 'exchange_from')->get();
        $exchange_to = clone $query;
        $exchange_to = $exchange_to->where('type', 'exchange_to')->get();
        $order_operation_orders_in = [];
        $order_operation_orders_out = [];
        $order_machine_supplies = [];
        $exchanges_from = [];
        $exchanges_to = [];
        $init_quantities = [];
        $last_quantities = [];


        foreach ($dates_array as $date) {
            $orders_in = TrackingMachineSupplies::where('date', $date)->where('supplie_id', $machine_supplie->supplie_id)
                ->where('machine_id', $request['machine_id'])->where('type', 'operation_in')->select(DB::raw('SUM(quantity) as total_quantity'))->first();
            if ($orders_in) {
                $order_operation_orders_in[] = $orders_in->total_quantity;
            } else {
                $order_operation_orders_in[] = 0;
            }
            $orders_out = TrackingMachineSupplies::where('date', $date)->where('supplie_id', $machine_supplie->supplie_id)
                ->where('machine_id', $request['machine_id'])->where('type', 'operation_out')->select(DB::raw('SUM(quantity) as total_quantity'))->first();
            if ($orders_out) {
                $order_operation_orders_out[] = $orders_out->total_quantity;
            } else {
                $order_operation_orders_out[] = 0;
            }
            $machine_supplie_arr = TrackingMachineSupplies::where('date', $date)->where('supplie_id', $machine_supplie->supplie_id)
                ->where('machine_id', $request['machine_id'])->where('type', 'machine_supplie')->select(DB::raw('SUM(quantity) as total_quantity'))->first();

            if ($machine_supplie_arr) {
                $order_machine_supplies[] = $machine_supplie_arr->total_quantity;
            } else {
                $order_machine_supplies[] = 0;
            }
            $exchange_from_arr = TrackingMachineSupplies::where('date', $date)->where('supplie_id', $machine_supplie->supplie_id)
                ->where('machine_id', $request['machine_id'])->where('type', 'exchange_from')->select(DB::raw('SUM(quantity) as total_quantity'))->first();

            if ($exchange_from_arr) {
                $exchanges_from[] = $exchange_from_arr->total_quantity;
            } else {
                $exchanges_from[] = 0;
            }
            $exchange_to_arr = TrackingMachineSupplies::where('date', $date)->where('supplie_id', $machine_supplie->supplie_id)
                ->where('machine_id', $request['machine_id'])->where('type', 'exchange_to')->select(DB::raw('SUM(quantity) as total_quantity'))->first();

            if ($exchange_to_arr) {
                $exchanges_to[] = $exchange_to_arr->total_quantity;
            } else {
                $exchanges_to[] = 0;
            }
            $init_quantity = TrackingMachineSupplies::where('date', $date)->where('supplie_id', $machine_supplie->supplie_id)
                ->where('machine_id', $request['machine_id'])->latest()->first()->init_quantity;
            if ($init_quantity) {
                $init_quantities[] = $init_quantity;
            } else {
                $init_quantities[] = 0;
            }
            $last_quantity = TrackingMachineSupplies::where('date', $date)->where('supplie_id', $machine_supplie->supplie_id)
                ->where('machine_id', $request['machine_id'])->latest()->first()->last_quantity;
            if ($init_quantity) {
                $last_quantities[] = $last_quantity;
            } else {
                $last_quantities[] = 0;
            }
        }

        // return $order_machine_supplies;

        return view('dashboard.reports.machine_supplies_used_inventory', compact('operation_orders_in', 'operation_orders_out', 'exchange_from', 'exchange_to', 'machine_supplies', 'machine_supplie', 'order_operation_orders_in', 'order_operation_orders_out', 'order_machine_supplies', 'dates', 'init_quantities', 'last_quantities', 'exchanges_from', 'exchanges_to', 'dates_array','request'));
    }
    
    
    

    public function damage_special_report(Request $request)
    {
        // return $request;
        $this->validate($request, [
            'date_from' => 'required',
            'date_to' => 'required',

        ]);
        $query = OperationOrder::query();
        if (isset($request['machine_id'])) {
            $operation_orders_ids = $query->where('machine_id', $request->machine_id);
        }
        $operation_orders_ids = $query->whereBetween('date', [$request->date_from, $request->date_to])
            ->with('operationOrderDetails', 'operationOrderResults', 'operationOrderResultDetails')
            ->pluck('id')->toArray();
        // return $operation_orders_ids;

        // $scraps = Item::whereIn('operat_ord_id', $operation_orders_ids)
        //     ->where('is_damage', 1)
        //     ->with(['quantities' => function ($q) {
        //         $q->where('quantity', "!=", 0);
        //     }])->whereHas('quantities', function ($q) {
        //         $q->where('quantity', "!=", 0);
        //     })
        //     ->where('damage_type', 'scrap')
        //     ->with('group', 'operationOrder')->get();


        // $pieces = Item::whereIn('operat_ord_id', $operation_orders_ids)
        //     ->where('is_damage', 1)
        //     ->with(['quantities' => function ($q) {
        //         $q->where('quantity', "!=", 0);
        //     }])->whereHas('quantities', function ($q) {
        //         $q->where('quantity', "!=", 0);
        //     })
        //     ->where('damage_type', 'pieces')
        //     ->with('group', 'operationOrder')->get();

        $specials = Item::whereIn('operat_ord_id', $operation_orders_ids)
            ->where('is_special', 1)
            ->with('group', 'operationOrder')->get();

        $specials2 = OperationOrderDetail::whereIn('operation_order_id', $operation_orders_ids)
            ->where('is_special', 1)
            ->whereNotNull('out_item_id')
            ->get();

        return view('dashboard.reports.damage_special', compact('request', 'operation_orders_ids', 'specials', 'specials2'));
    }

    public function scraps_report(Request $request)
    {
        $this->validate($request, [
            'date_from' => 'required',
            'date_to' => 'required',

        ]);
        $query = OperationOrder::query();
        if (isset($request['machine_id'])) {
            $operation_orders_ids = $query->where('machine_id', $request->machine_id);
        }
        $operation_orders_ids = $query->whereBetween('date', [$request->date_from, $request->date_to])
            ->with('operationOrderDetails', 'operationOrderResults', 'operationOrderResultDetails')
            ->pluck('id')->toArray();

        $scraps = Item::whereIn('operat_ord_id', $operation_orders_ids)
            ->where('is_damage', 1)
            ->with(['quantities' => function ($q) {
                $q->where('quantity', "!=", 0);
            }])->whereHas('quantities', function ($q) {
                $q->where('quantity', "!=", 0);
            })
            ->where('damage_type', 'scrap')
            ->with('group', 'operationOrder')->get();



        $resources = Item::with(['group', 'quantities' => function ($q) use ($request) {

            $q->where('ownerable_type', 'App\Models\Store');
        }])->whereHas('quantities')
            ->where(function ($q) use ($request) {
                $q->where('group_id', 71);
            })
            ->where("active", 1)
            ->get();


        // dd($resources);


        $stores = Store::get();
        return view('dashboard.reports.scraps_report', compact('resources', 'stores', 'request', 'operation_orders_ids', 'scraps'));
    }

    public function pieces_report(Request $request)
    {
        $this->validate($request, [
            'date_from' => 'required',
            'date_to' => 'required',

        ]);
        $query = OperationOrder::query();
        if (isset($request['machine_id'])) {
            $operation_orders_ids = $query->where('machine_id', $request->machine_id);
        }
        $operation_orders_ids = $query->whereBetween('date', [$request->date_from, $request->date_to])
            ->with('operationOrderDetails', 'operationOrderResults', 'operationOrderResultDetails')
            ->pluck('id')->toArray();


        $pieces = Item::whereIn('operat_ord_id', $operation_orders_ids)
            ->where('is_damage', 1)
            ->with(['quantities' => function ($q) {
                $q->where('quantity', "!=", 0);
            }])->whereHas('quantities', function ($q) {
                $q->where('quantity', "!=", 0);
            })
            ->where('damage_type', 'pieces')
            ->with('group', 'operationOrder')->get();



        $resources = Item::with(['group', 'quantities' => function ($q) use ($request) {

            $q->where('ownerable_type', 'App\Models\Store');
        }])->whereHas('quantities')
            ->where(function ($q) use ($request) {
                $q->where('group_id', 63);
            })
            ->where("active", 1)
            ->get();


        // dd($resources);


        $stores = Store::get();
        return view('dashboard.reports.pieces_report', compact('resources', 'stores', 'request', 'operation_orders_ids', 'pieces'));
    }
    
    //  public function damage_special_report(Request $request)
    // {
    //     $this->validate($request, [
    //         // 'machine_id' => 'required',
    //         'date_from' => 'required',
    //         'date_to' => 'required',

    //     ]);
    //     $query = OperationOrder::query();
    //     if (isset($request['machine_id'])) {
    //         $operation_orders_ids = $query->where('machine_id', $request->machine_id);
    //     }
    //     $operation_orders_ids = $query->whereBetween('date', [$request->date_from, $request->date_to])
    //         ->with('operationOrderDetails', 'operationOrderResults', 'operationOrderResultDetails')
    //         ->pluck('id')->toArray();

    //     // $operation_orders_ids = OperationOrder::where('machine_id', $request->machine_id)
    //     //     ->whereBetween('date', [$request->date_from, $request->date_to])
    //     //     ->with('operationOrderDetails', 'operationOrderResults', 'operationOrderResultDetails')
    //     //     ->pluck('id')->toArray();

    //     $scraps = Item::whereIn('operat_ord_id', $operation_orders_ids)
    //         ->where('is_damage', 1)
    //         ->with(['quantities' => function ($q) {
    //             $q->where('quantity', "!=", 0);
    //         }])->whereHas('quantities', function ($q) {
    //             $q->where('quantity', "!=", 0);
    //         })
    //         ->where('damage_type', 'scrap')
    //         ->with('group', 'operationOrder')->get();


    //     $pieces = Item::whereIn('operat_ord_id', $operation_orders_ids)
    //         ->where('is_damage', 1)
    //         ->with(['quantities' => function ($q) {
    //             $q->where('quantity', "!=", 0);
    //         }])->whereHas('quantities', function ($q) {
    //             $q->where('quantity', "!=", 0);
    //         })
    //         ->where('damage_type', 'pieces')
    //         ->with('group', 'operationOrder')->get();

    //     $specials = Item::whereIn('operat_ord_id', $operation_orders_ids)
    //         ->where('is_special', 1)
    //         ->with('group', 'operationOrder')->get();

    //     $specials2 = OperationOrderDetail::whereIn('operation_order_id', $operation_orders_ids)
    //         ->where('is_special', 1)
    //         ->whereNotNull('out_item_id')
    //         ->get();
    //     return view('dashboard.reports.damage_special', compact('request','operation_orders_ids', 'scraps', 'pieces', 'specials', 'specials2'));
    // }
    
    // public function scraps_report(Request $request)
    // {
    //     $this->validate($request, [
    //         'date_from' => 'required',
    //         'date_to' => 'required',

    //     ]);
    //     $query = OperationOrder::query();
    //     if (isset($request['machine_id'])) {
    //         $operation_orders_ids = $query->where('machine_id', $request->machine_id);
    //     }
    //     $operation_orders_ids = $query->whereBetween('date', [$request->date_from, $request->date_to])
    //         ->with('operationOrderDetails', 'operationOrderResults', 'operationOrderResultDetails')
    //         ->pluck('id')->toArray();

    //     $scraps = Item::whereIn('operat_ord_id', $operation_orders_ids)
    //         ->where('is_damage', 1)
    //         ->with(['quantities' => function ($q) {
    //             $q->where('quantity', "!=", 0);
    //         }])->whereHas('quantities', function ($q) {
    //             $q->where('quantity', "!=", 0);
    //         })
    //         ->where('damage_type', 'scrap')
    //         ->with('group', 'operationOrder')->get();



    //     $resources = Item::with(['group', 'quantities' => function ($q) use ($request) {

    //         $q->where('ownerable_type', 'App\Models\Store');
    //     }])->whereHas('quantities')
    //         ->where(function ($q) use ($request) {
    //             $q->where('group_id', 71);
    //         })
    //         ->where("active", 1)
    //         ->get();


    //     // dd($resources);


    //     $stores = Store::get();
    //     return view('dashboard.reports.scraps_report', compact('resources', 'stores', 'request', 'operation_orders_ids', 'scraps'));
    // }
    
    public function machinePerformance(Request $request) {
        $this->validate($request, [
            'machine_id'  => 'required',
            'date_from'   => 'required',
            'date_to'     => 'required',
        ]);
    
        $operationOrdersIds = OperationOrder::where('machine_id', $request->machine_id)
                                    ->whereBetween('date', [$request->date_from, $request->date_to])
                                    ->orderBy('date')->pluck('id')->toArray();
    
        $operationOrderResults = OperationOrderResult::whereIn('operation_order_id', $operationOrdersIds)->get();
    
        return view('dashboard.reports.machine-performance', compact('operationOrderResults'));
    }
    
    public function employeePerformance(Request $request) {
        $this->validate($request, [
            'date_from' => 'required',
            'date_to'   => 'required',
        ]);

        $query1 = OperationOrder::whereBetween('date', [$request->date_from, $request->date_to]);
        if($request->user_id) {
            $user = User::findOrFail($request->user_id);
            if($user->role_id == 7) {
                $query1->where('user_id', $request->user_id);
            }
            else if($user->role_id == 8) {
                $query1->where('supervisor_process', $request->user_id);
            }
            else if($user->role_id == 9) {
                $query1->where('supervisor_store', $request->user_id);
            }
        }
        $operationOrdersIds = $query1->orderBy('date')->pluck('id')->toArray();

        $query2 = OperationOrderResult::whereIn('operation_order_id', $operationOrdersIds);
        if($request->employee_id) {
            $user = Employee::findOrFail($request->employee_id);
            $query2->whereRaw("FIND_IN_SET(?, employee_id)", [$request->employee_id]);
        }
        $operationOrderResults = $query2->get();

        return view('dashboard.reports.employee-performance', compact('user', 'operationOrderResults'));
    }

    public function syncCurrentQuantities()
    {
        // 1. Get all supplies from the main table
        $supplies = DB::table('supplies')->get();
        $today = Carbon::now()->toDateString();
        $count = 0;

        foreach ($supplies as $supply) {
            // 2. Use updateOrInsert to prevent duplicates for today
            DB::table('supply_snapshots')->updateOrInsert(
                [
                    'supplie_id'    => $supply->id,
                    'snapshot_date' => $today,
                ],
                [
                    'quantity'   => $supply->quantity,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]
            );
            $count++;
        }

        // Return a JSON response instead of a redirect
        return response()->json([
            'status'  => 'success',
            'message' => "تمت مزامنة عدد ($count) من المستلزمات بنجاح لهذا اليوم.",
            'count'   => $count
        ]);
    }
}