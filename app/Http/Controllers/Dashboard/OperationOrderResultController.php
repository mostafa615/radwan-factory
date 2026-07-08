<?php
//------------------------------------------------------------------------------------------------- ~Mohamed Maher~ -------------------------------------------------------------------------------------------------------------------------//


namespace App\Http\Controllers\Dashboard;

use Storage;
use App\Item;
use App\User;
use App\Admin;
use App\Damage;
use App\Special;
use App\Student;
use App\Employee;
use App\Machines;
use App\Quantity;
use App\Reposite;
use App\Supplies;
use Pusher\Pusher;
use App\Department;
use App\SupplieTypes;
use App\MachineSupplie;
use App\OperationOrder;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use App\OperationOrderDetail;
use App\OperationOrderResult;
use Illuminate\Validation\Rule;
use App\TrackingMachineSupplies;
use Illuminate\Support\Facades\DB;
use App\OperationOrderResultDetail;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables as FacadesDataTables;

class OperationOrderResultController extends Controller
{
    public function __construct()
    {
        $this->middleware(['ability:admin,reade_operation_order_results'])->only('index');
        $this->middleware(['ability:admin,create_operation_order_results'])->only('create');
        $this->middleware(['ability:admin,update_operation_order_results'])->only('edit');
        $this->middleware(['ability:admin,delete_operation_order_results'])->only('destroy');
    }

    public function getData(Request $request)
    {
        $query = OperationOrderResult::query();
        if (isset($request['type']) && !empty($request['type'])) {
            // if (auth()->user()->hasRole('store_factor_response') || auth()->user()->hasRole('admin')) {
            //     $query->where('store_confirm', 0);
            // } else {
            //     $query->where('confirmed', 0);
            // }

            if (auth()->user()->hasRole('admin') || auth()->user()->hasRole('admin2') || auth()->user()->hasRole('store_factor_response') || auth()->user()->hasRole('safe_factor_response') || auth()->user()->hasRole('branch_factor_respons') || auth()->user()->hasRole('factor_response')) {
                $query->where('store_confirm', 0);
            } else {
                $query->where('confirmed', 0);
            }
        }
        // if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('مسئول فرع و مصنع' || auth()->user()->hasRole('مسئول مصنع') || auth()->user()->hasRole('مسئول مخزن و مصنع'))){
        // if (auth()->user()->hasRole('admin') || auth()->user()->hasRole('branch_factor_respons') || auth()->user()->hasRole('store_factor_response') || auth()->user()->hasRole('factor_response')) {
        //     $query->whereHas('operationOrder', function ($q) {
        //         $q->where('out_operation', 0);
        //     })
        //         ->with(['operationOrder' => function ($q) {
        //             $q->with('machine');
        //         }])
        //         ->get();
        // } else {
        //     $query = OperationOrderResult::toUser()->whereHas('operationOrder', function ($q) {
        //         $q->where('out_operation', 0);
        //     })->with(['operationOrder' => function ($q) {
        //         $q->with('machine');
        //     }]);
        // }

        if (auth()->user()->hasRole('admin') || auth()->user()->hasRole('admin2') || auth()->user()->hasRole('safe_factor_response') || auth()->user()->hasRole('branch_factor_respons') || auth()->user()->hasRole('store_factor_response') || auth()->user()->hasRole('factor_response')) {
            $query->whereHas('operationOrder', function ($q) {
                $q->where('out_operation', 0);
            })
                ->with([
                    'operationOrder' => function ($q) {
                        $q->with('machine');
                    }
                ])
                ->get();
        } else if (auth()->user()->hasRole('branch_factor_respons') || auth()->user()->hasRole('safe_factor_response') || auth()->user()->hasRole('factor_response')) {
            $query->whereHas('operationOrder', function ($q) {
                $q->where('out_operation', 0)->where('supervisor_process', auth()->user()->id);
            })
                ->with([
                    'operationOrder' => function ($q) {
                        $q->with('machine');
                    }
                ])
                ->get();
        } else if (auth()->user()->hasRole('store_factor_response')) {
            $query->whereHas('operationOrder', function ($q) {
                $q->where('out_operation', 0)->where('supervisor_store', auth()->user()->id);
            })
                ->with([
                    'operationOrder' => function ($q) {
                        $q->with('machine');
                    }
                ])
                ->get();
        } else {
            $query = OperationOrderResult::toUser()->whereHas('operationOrder', function ($q) {
                $q->where('out_operation', 0);
            })->with([
                        'operationOrder' => function ($q) {
                            $q->with('machine');
                        }
                    ]);
        }

        return FacadesDataTables::eloquent($query->with(['operationOrder', 'operationOrder.supervisor', 'user'])->latest())
            ->addColumn('action', function (OperationOrderResult $operationOrderResult) {
                $type = "action";
                return view("dashboard.operation_order_results.action", compact("operationOrderResult", "type"));
            })
            ->addColumn('confirm_notes', function (OperationOrderResult $operationOrderResult) {
                $type = "confirm_notes";
                return view("dashboard.operation_order_results.action", compact("operationOrderResult", "type"));
            })

            ->editColumn('operation_order_id', function (OperationOrderResult $operationOrderResult) {
                return '<a style="text-decoration: none;" target="_blank" href="' . route("dashboard.operation_orders.show", $operationOrderResult->operationOrder->id) . '" >' . $operationOrderResult->operationOrder->id . '</a>';
            })
            ->editColumn('date', function (OperationOrderResult $operationOrderResult) {
                return optional($operationOrderResult->operationOrder)->date;
            })
            ->editColumn('supervisor_name', function (OperationOrderResult $operationOrderResult) {
                return optional(optional($operationOrderResult->operationOrder)->supervisor)->name;
            })
            ->addColumn('machine_name', function (OperationOrderResult $operationOrderResult) {
                return optional(optional($operationOrderResult->operationOrder)->machine)->name;
            })
            ->addColumn('supervisor_process', function (OperationOrderResult $operationOrderResult) {
                if ($operationOrderResult->operationOrder->supervisor_process && $operationOrderResult->confirmed == 1) {
                    $name_id = explode(',', $operationOrderResult->operationOrder->supervisor_process);
                    $names = User::whereIn('id', $name_id)->pluck('name')->toArray();
                    return implode(", ", $names);
                }
                return '';
            })
            ->addColumn('suplies_name', function (OperationOrderResult $operationOrderResult) {
                $operationSupliesIds = explode(',', $operationOrderResult->operationOrderDetail->operation_suplies_id);
                $operationSupliesNames = Supplies::whereIn('id', $operationSupliesIds)->pluck('name')->toArray();
                return $operationSupliesNames;
            })

            ->editColumn('user_id', function (OperationOrderResult $operationOrderResult) {
                if ($operationOrderResult->operationOrder->user_id) {
                    $name = optional($operationOrderResult->operationOrder->user)->name;
                    return $name;
                }
                return '';
            })

            ->addColumn('item', function (OperationOrderResult $operationOrderResult) {
                if ($operationOrderResult->operationOrderDetail->item) {
                    $name = optional($operationOrderResult->operationOrderDetail->item)->name;
                    return $name;
                }
                return '';
            })
            ->addColumn('old_item_quantity', function (OperationOrderResult $operationOrderResult) {
                if ($operationOrderResult->old_item_quantity) {
                    $name = optional($operationOrderResult)->old_item_quantity;
                    return $name;
                }
                return '';
            })

            // ->editColumn('user_id', function (OperationOrderResult $operationOrderResult) {
            //     if ($operationOrderResult->user_id) {
            //         $name = optional($operationOrderResult->user)->name;

            //         return ' ' . $name . '<br>' . $operationOrderResult->confirmed_at;
            //     }
            //     return '';
            // })
            ->editColumn('employee_id', function (OperationOrderResult $operationOrderResult) {
                $empIds = explode(',', $operationOrderResult->employee_id);
                $employeesNames = Employee::whereIn('id', $empIds)->pluck('name')->toArray();
                return $employeesNames;
            })
            ->rawColumns(['action', 'confirm_notes', 'date', 'user_id', 'operation_order_id', 'supervisor_name', 'suplies_name'])
            ->toJson();
    }
    //

    public function getDataOut(Request $request)
    {
        $query = OperationOrderResult::query();
        if (isset($request['type']) && !empty($request['type'])) {
            // if (auth()->user()->hasRole('store_factor_response') || auth()->user()->hasRole('admin')) {
            //     $query->where('store_confirm', 0);
            // } else {
            //     $query->where('confirmed', 0);
            // }

            if (auth()->user()->hasRole('admin') || auth()->user()->hasRole('admin2') || auth()->user()->hasRole('store_factor_response') || auth()->user()->hasRole('safe_factor_response') || auth()->user()->hasRole('branch_factor_respons') || auth()->user()->hasRole('factor_response')) {
                $query->where('store_confirm', 0);
            } else {
                $query->where('confirmed', 0);
            }
        }
        // if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('مسئول فرع و مصنع' || auth()->user()->hasRole('مسئول مصنع') || auth()->user()->hasRole('مسئول مخزن و مصنع'))){
        // if (auth()->user()->hasRole('admin') || auth()->user()->hasRole('branch_factor_respons') || auth()->user()->hasRole('store_factor_response') || auth()->user()->hasRole('factor_response')) {
        //     $query->whereHas('operationOrder', function ($q) {
        //         $q->where('out_operation', 1);
        //     })->get();
        // } else {
        //     $query = OperationOrderResult::toUser()->whereHas('operationOrder', function ($q) {
        //         $q->where('out_operation', 1);
        //     });
        // }

        if (auth()->user()->hasRole('admin') || auth()->user()->hasRole('admin2') || auth()->user()->hasRole('safe_factor_response') || auth()->user()->hasRole('branch_factor_respons') || auth()->user()->hasRole('store_factor_response') || auth()->user()->hasRole('factor_response')) {
            $query->whereHas('operationOrder', function ($q) {
                $q->where('out_operation', 1);
            })
                ->with([
                    'operationOrder' => function ($q) {
                        $q->with('machine');
                    }
                ])
                ->get();
        } else if (auth()->user()->hasRole('branch_factor_respons') || auth()->user()->hasRole('safe_factor_response') || auth()->user()->hasRole('factor_response')) {
            $query->whereHas('operationOrder', function ($q) {
                $q->where('out_operation', 1)->where('supervisor_process', auth()->user()->id);
            })
                ->with([
                    'operationOrder' => function ($q) {
                        $q->with('machine');
                    }
                ])
                ->get();
        } else if (auth()->user()->hasRole('store_factor_response')) {
            $query->whereHas('operationOrder', function ($q) {
                $q->where('out_operation', 1)->where('supervisor_store', auth()->user()->id);
            })
                ->with([
                    'operationOrder' => function ($q) {
                        $q->with('machine');
                    }
                ])
                ->get();
        } else {
            $query = OperationOrderResult::toUser()->whereHas('operationOrder', function ($q) {
                $q->where('out_operation', 1);
            })->with([
                        'operationOrder' => function ($q) {
                            $q->with('machine');
                        }
                    ]);
        }


        return FacadesDataTables::eloquent($query->with(['operationOrder', 'operationOrder.supervisor', 'user'])->latest())
            ->addColumn('action', function (OperationOrderResult $operationOrderResult) {
                $type = "action";
                return view("dashboard.operation_order_results.action_out", compact("operationOrderResult", "type"));
            })
            ->addColumn('confirm_notes', function (OperationOrderResult $operationOrderResult) {
                $type = "confirm_notes";
                return view("dashboard.operation_order_results.action_out", compact("operationOrderResult", "type"));
            })
            ->editColumn('operation_order_id', function (OperationOrderResult $operationOrderResult) {
                return '<a style="text-decoration: none;" target="_blank" href="' . route("dashboard.operation_orders.show", $operationOrderResult->operationOrder->id) . '" >' . $operationOrderResult->operationOrder->id . '</a>';
            })
            ->editColumn('date', function (OperationOrderResult $operationOrderResult) {
                return optional($operationOrderResult->operationOrder)->date;
            })
            ->editColumn('supervisor_name', function (OperationOrderResult $operationOrderResult) {
                return optional(optional($operationOrderResult->operationOrder)->supervisor)->name;
            })
            ->addColumn('machine_name', function (OperationOrderResult $operationOrderResult) {
                return optional(optional($operationOrderResult->operationOrder)->machine)->name;
            })
            ->addColumn('client_name', function (OperationOrderResult $operationOrderResult) {
                return optional(optional($operationOrderResult->operationOrder))->client_name;
            })
            ->addColumn('suplies_name', function (OperationOrderResult $operationOrderResult) {
                $operationSupliesIds = explode(',', $operationOrderResult->operationOrderDetail->operation_suplies_id);
                $operationSupliesNames = Supplies::whereIn('id', $operationSupliesIds)->pluck('name')->toArray();
                return $operationSupliesNames;
            })

            ->addColumn('supervisor_process', function (OperationOrderResult $operationOrderResult) {
                if ($operationOrderResult->operationOrder->supervisor_process && $operationOrderResult->confirmed == 1) {
                    $name_id = explode(',', $operationOrderResult->operationOrder->supervisor_process);
                    $names = User::whereIn('id', $name_id)->pluck('name')->toArray();
                    return implode(", ", $names);
                }
                return '';
            })
            ->editColumn('user_id', function (OperationOrderResult $operationOrderResult) {
                if ($operationOrderResult->operationOrder->user_id) {
                    $name = optional($operationOrderResult->operationOrder->user)->name;
                    return $name;
                }
                return '';
            })
            ->addColumn('item', function (OperationOrderResult $operationOrderResult) {
                if ($operationOrderResult->operationOrderDetail->item_name) {
                    $name = optional($operationOrderResult->operationOrderDetail)->item_name;
                    return $name;
                }
                return '';
            })
            ->addColumn('old_item_quantity', function (OperationOrderResult $operationOrderResult) {
                if ($operationOrderResult->old_item_quantity) {
                    $name = optional($operationOrderResult)->old_item_quantity;
                    return $name;
                }
                return '';
            })
            // ->editColumn('user_id', function (OperationOrderResult $operationOrderResult) {
            //     if ($operationOrderResult->user_id) {
            //         $name = optional($operationOrderResult->user)->name;

            //         return ' ' . $name . '<br>' . $operationOrderResult->confirmed_at;
            //     }
            //     return '';
            // })
            ->editColumn('employee_id', function (OperationOrderResult $operationOrderResult) {
                $empIds = explode(',', $operationOrderResult->employee_id);
                $employeesNames = Employee::whereIn('id', $empIds)->pluck('name')->toArray();
                return $employeesNames;
            })
            ->rawColumns(['action', 'confirm_notes', 'date', 'user_id', 'operation_order_id', 'supervisor_name', 'suplies_name'])
            ->toJson();
    }
    //

    public function index(Request $request)
    {
        if (isset($request['type']) && !empty($request['type'])) {
            return view('dashboard.operation_order_results.index2');
        }
        return view('dashboard.operation_order_results.index');
    }

    public function indexOut(Request $request)
    {
        if (isset($request['type']) && !empty($request['type'])) {
            return view('dashboard.operation_order_results.index_out2');
        }
        return view('dashboard.operation_order_results.index_out');
    }

    public function show(OperationOrderResult $operationOrderResult)
    {
        $operationSupliesIds = explode(',', $operationOrderResult->operationOrderDetail->operation_suplies_id);

        $supplies = [];
        $used = [];
        $machine_id = $operationOrderResult->operationOrder->machine_id;
        foreach ($operationSupliesIds as $ids) {
            $supplies[] = Supplies::where('id', $ids)->first()->name;
            $used[] = MachineSupplie::select('used')
                ->where('machine_id', $machine_id)
                ->where('supplie_id', $ids)->first()->used;
        }
        // $supplies = Supplies::whereIn('id', $operationSupliesIds)->pluck('name')->toArray();
        // $machine_id = $operationOrderResult->operationOrder->machine_id;
        // $used = MachineSupplie::select('used')
        //     ->where('machine_id', $machine_id)
        //     ->whereIn('supplie_id', $operationSupliesIds)->pluck('used');

        $quantity = $operationOrderResult->operationOrderDetail->supplie_quantity_used;
        // $quantity = $operationOrderResult->total_used_length;
        return view('dashboard.operation_order_results.show', compact('operationOrderResult', 'supplies', 'quantity', 'used'));
    }

    public function showStore(OperationOrderResult $operationOrderResult)
    {
        $operationSupliesIds = explode(',', $operationOrderResult->operationOrderDetail->operation_suplies_id);

        $supplies = [];
        $used = [];
        $machine_id = $operationOrderResult->operationOrder->machine_id;
        foreach ($operationSupliesIds as $ids) {
            $supplies[] = Supplies::where('id', $ids)->first()->name;
            $used[] = MachineSupplie::select('used')
                ->where('machine_id', $machine_id)
                ->where('supplie_id', $ids)->first()->used;
        }
        // $supplies = Supplies::whereIn('id', $operationSupliesIds)->pluck('name')->toArray();
        // $machine_id = $operationOrderResult->operationOrder->machine_id;
        // $used = MachineSupplie::select('used')
        //     ->where('machine_id', $machine_id)
        //     ->whereIn('supplie_id', $operationSupliesIds)->pluck('used');

        $quantity = $operationOrderResult->operationOrderDetail->supplie_quantity_used;
        // $quantity = $operationOrderResult->total_used_length;
        return view('dashboard.operation_order_results.showStore', compact('operationOrderResult', 'supplies', 'quantity', 'used'));
    }
    //

    public function showOut(OperationOrderResult $operationOrderResult)
    {
        $operationSupliesIds = explode(',', $operationOrderResult->operationOrderDetail->operation_suplies_id);

        $supplies = [];
        $used = [];
        $machine_id = $operationOrderResult->operationOrder->machine_id;
        foreach ($operationSupliesIds as $ids) {
            $supplies[] = Supplies::where('id', $ids)->first()->name;
            $used[] = MachineSupplie::select('used')
                ->where('machine_id', $machine_id)
                ->where('supplie_id', $ids)->first()->used;
        }
        // $supplies = Supplies::whereIn('id', $operationSupliesIds)->pluck('name')->toArray();
        // $machine_id = $operationOrderResult->operationOrder->machine_id;
        // $used = MachineSupplie::select('used')
        //     ->where('machine_id', $machine_id)
        //     ->whereIn('supplie_id', $operationSupliesIds)->pluck('used');

        $quantity = $operationOrderResult->operationOrderDetail->supplie_quantity_used;
        // $quantity = optional($operationOrderResult->operationOrder)->total_used_length;

        $client = $operationOrderResult->operationOrder->client_name;
        return view('dashboard.operation_order_results.show_out', compact('operationOrderResult', 'supplies', 'quantity', 'client', 'used'));
    }

    public function showOutStore(OperationOrderResult $operationOrderResult)
    {
        $operationSupliesIds = explode(',', $operationOrderResult->operationOrderDetail->operation_suplies_id);

        $supplies = [];
        $used = [];
        $machine_id = $operationOrderResult->operationOrder->machine_id;
        foreach ($operationSupliesIds as $ids) {
            $supplies[] = Supplies::where('id', $ids)->first()->name;
            $used[] = MachineSupplie::select('used')
                ->where('machine_id', $machine_id)
                ->where('supplie_id', $ids)->first()->used;
        }
        // $supplies = Supplies::whereIn('id', $operationSupliesIds)->pluck('name')->toArray();
        // $machine_id = $operationOrderResult->operationOrder->machine_id;
        // $used = MachineSupplie::select('used')
        //     ->where('machine_id', $machine_id)
        //     ->whereIn('supplie_id', $operationSupliesIds)->pluck('used');

        $quantity = $operationOrderResult->operationOrderDetail->supplie_quantity_used;
        // $quantity = optional($operationOrderResult->operationOrder)->total_used_length;

        $client = $operationOrderResult->operationOrder->client_name;
        return view('dashboard.operation_order_results.showOutStore', compact('operationOrderResult', 'supplies', 'quantity', 'client', 'used'));
    }
    //

    // public function show(OperationOrderResult $operationOrderResult)
    // {

    //     return view('dashboard.operation_order_results.show', compact('operationOrderResult'));
    // }

    // public function showOut(OperationOrderResult $operationOrderResult)
    // {

    //     return view('dashboard.operation_order_results.show_out', compact('operationOrderResult'));
    // }

    public function create()
    {
        if (auth()->user()->hasRole('admin') || auth()->user()->hasRole('safe_factor_response') || auth()->user()->hasRole('branch_factor_respons') || auth()->user()->hasRole('store_factor_response') || auth()->user()->hasRole('factor_response')) {
            $operationOrders = OperationOrder::where('out_operation', 0)->latest()->get();
            $operationOrdrDetails = OperationOrderDetail::whereHas('operationOrder', function ($q) {
                $q->where('out_operation', 0);
            })->whereDoesntHave('operationOrderResults')->latest()->get();
        } else {
            $operationOrders = OperationOrder::toUser()->where('out_operation', 0)->latest()->get();
            $operationOrdersIds = $operationOrders->pluck('id')->toArray();
            $operationOrdrDetails = OperationOrderDetail::whereIn('operation_order_id', $operationOrdersIds)->whereDoesntHave('operationOrderResults')->latest()->get();
        }
        // dd($operationOrders);
        $damages = Damage::where('is_damage', 1)->where('group_id', 63)->latest()->get();
        $scrap = Damage::where('is_damage', 1)->where('group_id', 71)->latest()->get();
        $employees = Employee::where('branch_id', auth()->user()->branch_id)->where('job_id', 2)->where('active', 1)->latest()->get();

        return view('dashboard.operation_order_results.create', compact('operationOrders', 'operationOrdrDetails', 'scrap', 'damages', 'employees'));
    }

    public function createOut()
    {
        if (auth()->user()->hasRole('admin') || auth()->user()->hasRole('safe_factor_response') || auth()->user()->hasRole('branch_factor_respons') || auth()->user()->hasRole('store_factor_response') || auth()->user()->hasRole('factor_response')) {
            $operationOrders = OperationOrder::where('out_operation', 1)->latest()->get();
            $operationOrdrDetails = OperationOrderDetail::whereHas('operationOrder', function ($q) {
                $q->where('out_operation', 1)->where('date', '>=', '2023-12-01');
            })->whereDoesntHave('operationOrderResults')->latest()->get();
        } else {
            $operationOrders = OperationOrder::toUser()->where('out_operation', 1)->latest()->get();
            $operationOrdersIds = $operationOrders->pluck('id')->toArray();
            $operationOrdrDetails = OperationOrderDetail::whereIn('operation_order_id', $operationOrdersIds)->whereDoesntHave('operationOrderResults')->latest()->get();
        }
        // dd($operationOrders);
        $damages = Damage::where('is_damage', 1)->where('group_id', 63)->latest()->get();
        $scrap = Damage::where('is_damage', 1)->where('group_id', 71)->latest()->get();
        $employees = Employee::where('branch_id', auth()->user()->branch_id)->where('job_id', 2)->where('active', 1)->latest()->get();

        return view('dashboard.operation_order_results.create_out', compact('operationOrders', 'operationOrdrDetails', 'scrap', 'damages', 'employees'));
    }


    /**
     * Momaher
     */

    //osama


    public function store(Request $request)
    {

        // return $request->all();
        $request->validate([
            'order_details_id' => 'required',
            'actual_output' => 'required',
            'old_item_quantity' => 'required',
            'notes',
        ]);

        $request_data = $request->except(['damage_type', 'old_damage_id', 'damage_quantity', 'damage_length', 'damage_width', 'damage_thickness', 'damage_price', 'damage_weight']);

        $request_data['operation_order_id'] = OperationOrderDetail::where('id', $request->order_details_id)->first()->operation_order_id;
        $request_data['order_details_id'] = $request->order_details_id;
        if (isset($request['employee_id']) && !empty($request['employee_id'])) {
            $request_data['employee_id'] = implode(',', $request->employee_id);
        }
        DB::beginTransaction();
        $operationOrderResult = OperationOrderResult::create($request_data);
        $operationOrder = OperationOrder::where('id', $operationOrderResult->operation_order_id)->first();
        $operationOrderDetail = OperationOrderDetail::where('id', $operationOrderResult->order_details_id)->first();
        // if(isset($reqeust['supply_quantity']) && isset($reqeust['supply_length'])){
        //     $operationOrderDetail->supplie_quantity_used = $request['supply_quantity'] * $request['supply_length'];
        //     $operationOrderDetail->save();
        // }

        $machine = Machines::where('id', $operationOrder->machine_id)->first();

        $store = DB::table('stores')
            ->select('id', 'name')
            ->where('id', $machine->store_id)
            ->first();

        $supplies_id = explode(',', $operationOrderDetail->operation_suplies_id);

        /**
         * Tracking Machine supplie
         */
        foreach ($supplies_id as $supplie_id) {
            $trackingMachineSupplie = TrackingMachineSupplies::where('date', $operationOrder->date)
                ->where('supplie_id', $supplie_id)
                ->where('machine_id', $operationOrder->machine_id)
                ->first();
            $new_tracking = TrackingMachineSupplies::create([
                'machine_id' => $operationOrder->machine_id,
                'supplie_id' => $supplie_id,
                'date' => $operationOrder->date,
                'type' => 'operation_in',
                'quantity' => -$request['supply_quantity'] * $request['supply_length'],
                // 'quantity' => $operationOrderDetail->supplie_quantity_used,
                'operation_order_id' => $operationOrder->id,
                'operation_order_result_id' => $operationOrderResult->id
            ]);
            // return $trackingMachineSupplie;
            if (!$trackingMachineSupplie) {
                $last_tracking = TrackingMachineSupplies::where('supplie_id', $supplie_id)
                    ->where('machine_id', $operationOrder->machine_id)->where('id', '!=', $new_tracking->id)->orderBy('date', 'DESC')->latest()->first();
                if ($last_tracking) {
                    $new_tracking->init_quantity = $last_tracking->last_quantity;
                    $new_tracking->last_quantity = $new_tracking->init_quantity + $new_tracking->quantity;
                    $new_tracking->save();
                } else {
                    $new_tracking->init_quantity = MachineSupplie::where('machine_id', $operationOrder->machine_id)->where('supplie_id', $supplie_id)->first()->used;
                    $new_tracking->last_quantity = $new_tracking->init_quantity + $new_tracking->quantity;
                    $new_tracking->save();
                }
            } else {
                $new_tracking->init_quantity = $trackingMachineSupplie->init_quantity;
                $new_tracking->last_quantity = $trackingMachineSupplie->last_quantity + $new_tracking->quantity;
                $new_tracking->save();
            }
        }
        /**
         * Decrement Used from supplies
         */

        $machine_supplies = MachineSupplie::where('machine_id', $operationOrder->machine_id)->whereIn('supplie_id', $supplies_id)->get();
        foreach ($machine_supplies as $item) {
            $supply_used = Supplies::where('id', $item->supplie_id)->first()->used;
            $item->decrement('used', $request['supply_quantity'] * $request['supply_length']);
            $item->quantity = ceil($item->used / $supply_used);
            $item->save();
        }

        $damageWeightSum = 0;
        if ($request->damage_quantity) {
            $counter = 0;
            foreach ($request->damage_quantity as $item) {
                $damageWeightSum += $request->damage_weight[$counter];

                $orderResultDetail = OperationOrderResultDetail::create([
                    'operation_order_id' => $operationOrderResult->operation_order_id,
                    'order_details_id' => $operationOrderResult->order_details_id,
                    'order_results_id' => $operationOrderResult->id,
                    'damage_type' => $request->damage_type[$counter],
                    'damage_name' => $request->damage_name[$counter] ?? Item::where('id', $request->old_damage_id[$counter])->first()->name,
                    // 'damage_name' => $request->damage_name[$counter],
                    'damage_quantity' => $request->damage_quantity[$counter],
                    'damage_length' => $request->damage_length[$counter],
                    'damage_width' => $operationOrderDetail->width,
                    'damage_thickness' => $operationOrderResult->thickness,
                    'damage_price' => $request->damage_price[$counter],
                    'damage_weight' => $request->damage_weight[$counter],
                ]);
                if ($request->old_damage_id != null) {
                    $orderResultDetail->old_damage_id = $request->old_damage_id[$counter];
                    $orderResultDetail->save();
                }
                $counter++;
            }
        }

        $itemQuantity = Quantity::where('ownerable_type', 'App\Models\Store')
            ->where('ownerable_id', $store->id)
            ->where('item_id', $operationOrderDetail->item_id)
            ->first();

        if ($request->old_item_quantity == 0 || $request->old_item_quantity + $damageWeightSum > $itemQuantity->quantity) {
            session()->flash('error', 'انتبه: لا يمكن ان تكون كمية الخامة المستخدمة اكبر من المتاحة للألة: ' . $itemQuantity->quantity);
            return back();
        }

        DB::commit();

        //send notifications to Responsable users of machine branch

        $machineBranch = DB::table('branches')->where('name', $store->name)->first();
        $rolesIds = DB::table('roles')->whereIn('name', ['factor_response', 'branch_factor_respons', 'safe_factor_response'])->pluck('id')->toArray();
        $usersRespons = DB::table('users')->where('branch_id', $machineBranch->id)->whereIn('role_id', $rolesIds)->pluck('id')->toArray();

        for ($index = 0; $index < count($usersRespons); $index++) {
            $this->push_notification(['user_id' => $usersRespons[$index], 'url' => url('operation_orders')]);
        }
    
        $operationOrder->tracks()->where('step_name', 'machine_manager')->update([
            'status' => 'approved',
            'user_id' => auth()->user()->id,
            'notes' => $request->notes ?? null,
            'action_at' => now(),
        ]);

        session()->flash('success', __('site.added_successfully'));
        return redirect()->route('dashboard.operation_order_results.index');
    }


    // public function store(Request $request)
    // {
    //     // dd($request->all());
    //     $request->validate([
    //         'order_details_id' => 'required',
    //         'actual_output' => 'required',
    //         'old_item_quantity' => 'required',
    //         'notes',
    //     ]);

    //     $request_data = $request->except(['damage_type', 'old_damage_id', 'damage_quantity', 'damage_length', 'damage_width', 'damage_thickness', 'damage_price', 'damage_weight']);

    //     $request_data['operation_order_id'] = OperationOrderDetail::where('id', $request->order_details_id)->first()->operation_order_id;
    //     $request_data['order_details_id'] = $request->order_details_id;
    //     if (isset($request['employee_id']) && !empty($request['employee_id'])) {
    //         $request_data['employee_id'] = implode(',', $request->employee_id);
    //     }
    //     DB::beginTransaction();
    //     $operationOrderResult = OperationOrderResult::create($request_data);
    //     $operationOrder = OperationOrder::where('id', $operationOrderResult->operation_order_id)->first();
    //     $operationOrderDetail = OperationOrderDetail::where('id', $operationOrderResult->order_details_id)->first();
    //     // if(isset($reqeust['supply_quantity']) && isset($reqeust['supply_length'])){
    //     //     $operationOrderDetail->supplie_quantity_used = $request['supply_quantity'] * $request['supply_length'];
    //     //     $operationOrderDetail->save();
    //     // }

    //     $machine = Machines::where('id', $operationOrder->machine_id)->first();

    //     // dd(  $machine);//1//3
    // //    dd($operationOrder->machine_id);//1
    //     $store = DB::table('stores')
    //         ->select('id', 'name')
    //         ->where('id', $machine->store_id)
    //         ->first();

    //     $supplies_id = explode(',', $operationOrderDetail->operation_suplies_id);

    //     // dd(  $supplies_id);//108

    //     /**
    //      * Tracking Machine supplie
    //      */
    //     foreach ($supplies_id as $supplie_id) {
    //         $trackingMachineSupplie = TrackingMachineSupplies::where('date', $operationOrder->date)
    //             ->where('supplie_id', $supplie_id)
    //             ->where('machine_id', $operationOrder->machine_id)
    //             ->first();
    //     // dd(  $trackingMachineSupplie->init_quantity);//null

    //         $new_tracking = TrackingMachineSupplies::create([
    //             'machine_id' => $operationOrder->machine_id,
    //             'supplie_id' => $supplie_id,
    //             'date' => $operationOrder->date,
    //             'type' => 'operation_in',
    //             'quantity' => -$request['supply_quantity'] * $request['supply_length'],
    //             // 'quantity' => $operationOrderDetail->supplie_quantity_used,
    //             'operation_order_id' => $operationOrder->id,
    //             'operation_order_result_id' => $operationOrderResult->id
    //         ]);

    //     // dd(  $new_tracking);

    //         // dd($trackingMachineSupplie);
    //         if (!$trackingMachineSupplie) {

    //             $last_tracking = TrackingMachineSupplies::where('supplie_id', $supplie_id)
    //                 ->where('machine_id', $operationOrder->machine_id)
    //                 ->where('id', '!=', $new_tracking->id)
    //                 ->orderBy('date', 'DESC')
    //                 ->latest()
    //                 ->first();
    //             // dd($last_tracking);
    //             if ($last_tracking) {

    //                 $new_tracking->init_quantity = $last_tracking->last_quantity;
    //                 $new_tracking->last_quantity = $new_tracking->init_quantity + $new_tracking->quantity;
    //                 $new_tracking->save();
    //             } else {

    //                 $new_tracking->init_quantity = MachineSupplie::where('machine_id', $operationOrder->machine_id)->where('id', $supplie_id)->first()->used;
    //                 $new_tracking->last_quantity = $new_tracking->init_quantity + $new_tracking->quantity;

    //                 // dd($new_tracking->last_quantity);
    //                 $new_tracking->save();

    //                 // $machineSupplie = MachineSupplie::where('machine_id', $operationOrder->machine_id)
    //                 //     ->where('id', $supplie_id)
    //                 //     ->first();

    //                 // dd( $machineSupplie);

    //                 // if (!$machineSupplie) {
    //                 //     MachineSupplie::create([
    //                 //         'machine_id' => $operationOrder->machine_id,
    //                 //         'supplie_id' => $supplie_id,
    //                 //         'used' => 0,
    //                 //     ]);


    //                 //     $machineSupplie = MachineSupplie::where('machine_id', $operationOrder->machine_id)
    //                 //         ->where('supplie_id', $supplie_id)
    //                 //         ->first();
    //                 // }


    //                 // $new_tracking->init_quantity = $machineSupplie->used;
    //                 // $new_tracking->last_quantity = $new_tracking->init_quantity + $new_tracking->quantity;
    //                 // $new_tracking->save();
    //             }
    //         } else {

    //             $new_tracking->init_quantity = $trackingMachineSupplie->init_quantity;
    //             // dd( $new_tracking->init_quantity);
    //             $new_tracking->last_quantity = $trackingMachineSupplie->last_quantity + $new_tracking->quantity;
    //             $new_tracking->save();
    //         }
    //     }
    //     /**
    //      * Decrement Used from supplies
    //      */

    //      $machine_supplies = MachineSupplie::where('machine_id', $operationOrder->machine_id)->whereIn('id', $supplies_id)->get();
    //     //  dd($machine_supplies);
    //      foreach ($machine_supplies as $item) {
    //          $supply_used = Supplies::where('id', $item->supplie_id)->first()->used;
    //         //  dd($supply_used);
    //          $item->decrement('used', $request['supply_quantity'] * $request['supply_length']);
    //          $item->quantity = ceil($item->used / $supply_used);
    //          $item->save();

    //     }

    //     $damageWeightSum = 0;
    //     if ($request->damage_quantity) {
    //         $counter = 0;
    //         foreach ($request->damage_quantity as $item) {
    //             $damageWeightSum += $request->damage_weight[$counter];

    //             $orderResultDetail = OperationOrderResultDetail::create([
    //                 'operation_order_id'        => $operationOrderResult->operation_order_id,
    //                 'order_details_id'      => $operationOrderResult->order_details_id,
    //                 'order_results_id'      => $operationOrderResult->id,
    //                 'damage_type'       => $request->damage_type[$counter],
    //                 'damage_name'       => $request->damage_name[$counter],
    //                 'damage_quantity'       => $request->damage_quantity[$counter],
    //                 'damage_length'     => $request->damage_length[$counter],
    //                 'damage_width'      => $operationOrderDetail->width,
    //                 'damage_thickness'      => $operationOrderResult->thickness,
    //                 'damage_price'      => $request->damage_price[$counter],
    //                 'damage_weight'     => $request->damage_weight[$counter],
    //             ]);
    //             if ($request->old_damage_id != null) {
    //                 $orderResultDetail->old_damage_id = $request->old_damage_id[$counter];
    //                 $orderResultDetail->save();
    //             }
    //             $counter++;
    //         }
    //     }

    //     $itemQuantity = Quantity::where('ownerable_type', 'App\Models\Store')
    //         ->where('ownerable_id', $store->id)
    //         ->where('item_id', $operationOrderDetail->item_id)
    //         ->first();

    //     if($request->old_item_quantity == 0 || $request->old_item_quantity + $damageWeightSum > $itemQuantity->quantity) {
    //         session()->flash('error', 'انتبه: لا يمكن ان تكون كمية الخامة المستخدمة اكبر من المتاحة للألة: ' . $itemQuantity->quantity);
    //         return back();
    //     }

    //     DB::commit();

    //     //send notifications to Responsable users of machine branch

    //     $machineBranch = DB::table('branches')->where('name', $store->name)->first();
    //     $rolesIds = DB::table('roles')->whereIn('name', ['factor_response', 'branch_factor_respons', 'safe_factor_response'])->pluck('id')->toArray();
    //     $usersRespons = DB::table('users')->where('branch_id', $machineBranch->id)->whereIn('role_id', $rolesIds)->pluck('id')->toArray();

    //     for ($index = 0; $index < count($usersRespons); $index++) {
    //         $this->push_notification(['user_id' => $usersRespons[$index], 'url' => url('operation_orders')]);
    //     }

    //     session()->flash('success', __('site.added_successfully'));
    //     return redirect()->route('dashboard.operation_order_results.index');
    // }
    public function del_edit($operation_id)
    {
        //We have operation order id
        $operation_order_detail = OperationOrderDetail::find($operation_id);
        $operation_order = OperationOrder::find($operation_order_detail->operation_order_id);

        $operation_order->update([
            'machine_access' => 0,
            'machine_edit' => 1
        ]);
        $operation_order_detail->update([
            'active' => 0,
        ]);

        session()->flash('success', 'تم التعديل بنجاح');
        return redirect()->back();
    }
    public function storeOut(Request $request)
    {
        // return $request;
        $request->validate([
            'order_details_id' => 'required',
            'actual_output' => 'required',
            'old_item_quantity' => 'required',
            'total_used_length' => 'required',
            'notes',
        ]);

        DB::beginTransaction();

        $request_data = $request->except(['damage_type', 'old_damage_id', 'damage_quantity', 'damage_length', 'damage_width', 'damage_thickness', 'damage_price', 'damage_weight']);
        // dd($request_data);
        $request_data['operation_order_id'] = OperationOrderDetail::where('id', $request->order_details_id)->first()->operation_order_id;
        $request_data['order_details_id'] = $request->order_details_id;
        if (isset($request['employee_id']) && !empty($request['employee_id'])) {
            $request_data['employee_id'] = implode(',', $request->employee_id);
        }
        $operationOrderResult = OperationOrderResult::create($request_data);
        $operationOrder = OperationOrder::where('id', $operationOrderResult->operation_order_id)->first();
        $operationOrderDetail = OperationOrderDetail::where('id', $operationOrderResult->order_details_id)->first();

        $operationOrder->total_used_length = $request->total_used_length;
        $operationOrder->save();

        $machine = Machines::where('id', $operationOrder->machine_id)->first();
        $store = DB::table('stores')
            ->select('id', 'name')
            ->where('id', $machine->store_id)
            ->first();

        $supplies_id = explode(',', $operationOrderDetail->operation_suplies_id);

        /**
         * Tracking Machine supplie
         */
        foreach ($supplies_id as $supplie_id) {
            $trackingMachineSupplie = TrackingMachineSupplies::where('date', $operationOrder->date)
                ->orderBy('date', 'DESC')
                ->where('supplie_id', $supplie_id)
                ->where('machine_id', $operationOrder->machine_id)
                ->latest()->first();
            $new_tracking = TrackingMachineSupplies::create([
                'machine_id' => $operationOrder->machine_id,
                'supplie_id' => $supplie_id,
                'date' => $operationOrder->date,
                'type' => 'operation_out',
                'quantity' => -$request['supply_quantity'] * $request['supply_length'],
                // 'quantity' => $operationOrderDetail->supplie_quantity_used,
                'operation_order_id' => $operationOrder->id,
                'operation_order_result_id' => $operationOrderResult->id
            ]);
            // return $trackingMachineSupplie;
            if (!$trackingMachineSupplie) {
                $last_tracking = TrackingMachineSupplies::where('supplie_id', $supplie_id)
                    ->where('machine_id', $operationOrder->machine_id)->orderBy('date', 'DESC')->where('id', '!=', $new_tracking->id)->latest()->first();
                if ($last_tracking) {
                    $new_tracking->init_quantity = $last_tracking->last_quantity;
                    $new_tracking->last_quantity = $new_tracking->init_quantity + $new_tracking->quantity;
                    $new_tracking->save();
                } else {

                    $new_tracking->init_quantity = MachineSupplie::where('machine_id', $operationOrder->machine_id)->where('supplie_id', $supplie_id)->first()->used ?? 0;
                    $new_tracking->last_quantity = $new_tracking->init_quantity + $new_tracking->quantity;
                    $new_tracking->save();
                }
            } else {
                $new_tracking->init_quantity = $trackingMachineSupplie->init_quantity;
                $new_tracking->last_quantity = $trackingMachineSupplie->last_quantity + $new_tracking->quantity;
                $new_tracking->save();
            }
        }
        
        /**
         * Update operation order details with supplie_quantity_used
         */
        $operationOrderDetail->supplie_quantity_used = $request['supply_quantity'] * $request['supply_length'];
        $operationOrderDetail->save();
        
        /**
         * Decrement Used from supplies
         */
        $machine_supplies = MachineSupplie::where('machine_id', $operationOrder->machine_id)->whereIn('supplie_id', $supplies_id)->get();
        foreach ($machine_supplies as $item) {
            $item->decrement('used', $request['supply_quantity'] * $request['supply_length']);
        }

        if ($request->damage_quantity) {
            $counter = 0;
            foreach ($request->damage_quantity as $item) {
                $orderResultDetail = OperationOrderResultDetail::create([
                    'operation_order_id' => $operationOrderResult->operation_order_id,
                    'order_details_id' => $operationOrderResult->order_details_id,
                    'order_results_id' => $operationOrderResult->id,
                    'damage_type' => $request->damage_type[$counter],
                    'damage_name' => $request->damage_name[$counter],
                    'damage_quantity' => $request->damage_quantity[$counter],
                    'damage_length' => $request->damage_length[$counter],
                    'damage_width' => $operationOrderDetail->width,
                    'damage_thickness' => $operationOrderResult->thickness,
                    'damage_price' => $request->damage_price[$counter],
                    'damage_weight' => $request->damage_weight[$counter],
                ]);
                if ($request->old_damage_id != null) {
                    $orderResultDetail->old_damage_id = $request->old_damage_id[$counter];
                    $orderResultDetail->save();
                }
                $counter++;
            }
            /**
             * Tracking Machine supplie
             */
            $trackingMachineSupplie = TrackingMachineSupplies::where('date', $operationOrderResult->date)->first();
            $new_tracking = TrackingMachineSupplies::create([
                'machine_id' => $operationOrderResult->operationOrder->machine_id,
                'supplie_id' => $operationOrderResult->operationOrderDetail->operation_suplies_id,
                'date' => $operationOrderResult->operationOrder->date,
                'type' => 'operation_out',
                'quantity' => $operationOrderResult->operationOrderDetail->supplie_quantity_used,
                'operation_order_id' => $orderResultDetail->id
            ]);
            if (!$trackingMachineSupplie) {
                $new_tracking->init_quantity = TrackingMachineSupplies::latest()->first()->init_quantity;
                $new_tracking->save();
            } else {
                $new_tracking->init_quantity = $trackingMachineSupplie->init_quantity;
                $new_tracking->save();
            }
        }

        DB::commit();
        //send notifications to Responsable users of machine branch

        $machineBranch = DB::table('branches')->where('name', $store->name)->first();
        $rolesIds = DB::table('roles')->whereIn('name', ['factor_response', 'branch_factor_respons', 'safe_factor_response'])->pluck('id')->toArray();
        $usersRespons = DB::table('users')->where('branch_id', $machineBranch->id)->whereIn('role_id', $rolesIds)->pluck('id')->toArray();

        for ($index = 0; $index < count($usersRespons); $index++) {
            $this->push_notification(['user_id' => $usersRespons[$index], 'url' => url('operation_orders')]);
        }
    
        $operationOrder->tracks()->where('step_name', 'machine_manager')->update([
            'status' => 'approved',
            'user_id' => auth()->user()->id,
            'notes' => $request->notes ?? null,
            'action_at' => now(),
        ]);

        session()->flash('success', __('site.added_successfully'));
        return redirect()->route('dashboard.operation_order_results.index_out');
    }



    public function edit($operationOrderResult)
    {
        $operationOrders = OperationOrder::latest()->get();
        $operationOrderResult = OperationOrderResult::find($operationOrderResult);
        $operationOrdersIds = $operationOrders->pluck('id')->toArray();
        $operationOrdrDetails = OperationOrderDetail::whereIn('operation_order_id', $operationOrdersIds)->latest()->get();


        return view('dashboard.operation_order_results.edit', compact('operationOrderResult', 'operationOrdrDetails', 'operationOrders'));
    }

    public function update(Request $request, $operationOrderResult)
    {
        $request->validate([
            'order_details_id' => 'required',
            'actual_output' => 'required',
            'notes',
        ]);
        $request_data = $request->except(['damage_type', 'old_damage_id', 'damage_quantity', 'damage_length', 'damage_width', 'damage_thickness', 'damage_price', 'damage_weight']);

        $operationOrderResult = OperationOrderResult::find($operationOrderResult);
        $operationOrder = OperationOrder::where('id', $operationOrderResult->operation_order_id)->first();
        $operationOrderDetail = OperationOrderDetail::where('id', $operationOrderResult->order_details_id)->first();

        $machine = Machines::where('id', $operationOrder->machine_id)->first();
        $store = DB::table('stores')
            ->select('id', 'name')
            ->where('id', $machine->store_id)
            ->first();

        if ($operationOrderDetail->out_item_id) {
            $quantity = Quantity::where('ownerable_type', 'App\Models\Store')
                ->where('ownerable_id', $store->id)
                ->where('item_id', $operationOrderDetail->out_item_id)
                ->first();
            if ($operationOrderResult->confirmed) {
                $quantity->decrement('quantity', $operationOrderResult->actual_output);
                $quantity->save();
            }
        }
        if ($operationOrderDetail->item_id) {
            $quantity = Quantity::where('ownerable_type', 'App\Models\Store')
                ->where('ownerable_id', $store->id)
                ->where('item_id', $operationOrderDetail->item_id)
                ->first();
            if ($operationOrderResult->confirmed) {
                $quantity->increment('quantity', $operationOrderResult->old_item_quantity);
                $quantity->save();
            }
        }
        $request_data['confirmed'] = 0;
        $operationOrderResult->update($request_data);

        session()->flash('success', __('site.updated_successfully'));
        return redirect()->route('dashboard.operation_order_results.index');
    }


    public function destroy($operationOrderResult)
    {
        $operationOrderResult = OperationOrderResult::find($operationOrderResult);

        if (!empty($operationOrderResult)) {
            $operationOrder = OperationOrder::where('id', $operationOrderResult->operation_order_id)->first();
            $operationOrderDetail = OperationOrderDetail::where('id', $operationOrderResult->order_details_id)->first();

            $machine = Machines::where('id', $operationOrder->machine_id)->first();
            $store = DB::table('stores')
                ->select('id', 'name')
                ->where('id', $machine->store_id)
                ->first();

            $supplies_id = explode(',', $operationOrderDetail->operation_suplies_id);

            $machine_supplies = MachineSupplie::where('machine_id', $operationOrder->machine_id)->whereIn('supplie_id', $supplies_id)->get();
            foreach ($machine_supplies as $item) {
                $item->increment('used', $operationOrderDetail->supplie_quantity_used);
            }

            if ($operationOrderDetail->out_item_id) {
                $quantity = Quantity::where('ownerable_type', 'App\Models\Store')
                    ->where('ownerable_id', $store->id)
                    ->where('item_id', $operationOrderDetail->out_item_id)
                    ->first();
                if ($operationOrderResult->store_confirm) {
                    $quantity->decrement('quantity', $operationOrderResult->actual_output);
                    $quantity->save();
                }
            }
            if ($operationOrderDetail->item_id) {
                $quantity = Quantity::where('ownerable_type', 'App\Models\Store')
                    ->where('ownerable_id', $store->id)
                    ->where('item_id', $operationOrderDetail->item_id)
                    ->first();
                if ($operationOrderResult->store_confirm) {
                    $quantity->increment('quantity', $operationOrderResult->old_item_quantity);
                    $quantity->save();
                }
            }

            if ($operationOrderDetail->is_special) {
                $item = DB::table('items')
                    ->select('id', 'name')
                    ->where('operat_ord_id', $operationOrder->id)
                    ->where('is_special', 1)
                    ->first();

                if ($item) {
                    $quantity = Quantity::where('ownerable_type', 'App\Models\Store')
                        ->where('ownerable_id', $store->id)
                        ->where('item_id', $item->id)
                        ->first();

                    if ($operationOrderResult->store_confirm) {
                        $quantity->decrement('quantity', $operationOrderResult->actual_output);
                        $quantity->save();
                    }
                }
            }

            if (!@empty($operationOrderResult->orderResultDetails)) {
                foreach ($operationOrderResult->orderResultDetails as $orderResultDetail) {

                    $item = DB::table('items')
                        ->select('id', 'name')
                        ->where('operat_ord_id', $operationOrder->id)
                        ->where('is_damage', 1)
                        ->where('name', $orderResultDetail->damage_name)
                        ->first();
                    if ($item) {
                        $quantity = Quantity::where('ownerable_type', 'App\Models\Store')
                            ->where('ownerable_id', $store->id)
                            ->where('item_id', $item->id)
                            ->first();
                        $quantity->decrement('quantity', $orderResultDetail->damage_quantity);
                        $quantity->save();

                        $orderResultDetail->delete();
                    }
                }
            }
            TrackingMachineSupplies::where('operation_order_result_id', $operationOrderResult->id)->delete();
            $operationOrder->update(['store_edit' => 1]);
            $operationOrderResult->delete();
        }

        session()->flash('success', __('site.deleted_successfully'));
        return back();
    }
    //

    public function order_result_delete_detail($id)
    {
        $orderResultDetail = OperationOrderResultDetail::where('id', $id)->first();
        $operationOrderResult = OperationOrderResult::where('id', $orderResultDetail->order_results_id)->first();
        $operationOrder = OperationOrder::where('id', $orderResultDetail->operation_order_id)->first();
        $machine = Machines::where('id', $operationOrder->machine_id)->first();
        $store = DB::table('stores')
            ->select('id', 'name')
            ->where('id', $machine->store_id)
            ->first();

        $item = DB::table('items')
            ->select('id', 'name')
            ->where('operat_ord_id', $operationOrder->id)
            ->where('is_damage', 1)
            ->where('name', $orderResultDetail->damage_name)
            ->first();

        $quantity = Quantity::where('ownerable_type', 'App\Models\Store')
            ->where('ownerable_id', $store->id)
            ->where('item_id', $item->id)
            ->first();
        $quantity->decrement('quantity', $orderResultDetail->damage_quantity);
        $quantity->save();



        $orderResultDetail->delete();
        session()->flash('success', __('site.deleted_successfully'));
        return back();
    }
    public function getOpertOrderInfo(Request $request)
    {
        if (!$request->operation_order_detail_id) {
            $html = '';
            $html .= '<h5>برجاء اختيار امر التشغيل</h5>';
        } else {
            $html = '';
            $operationOrderDetails = OperationOrderDetail::where('id', $request->operation_order_detail_id)->get();
            foreach ($operationOrderDetails as $operationOrderDetail) {
                $machine_supplies_id = explode(',', $operationOrderDetail->operation_suplies_id);
                // $machineSupplies = MachineSupplie::where("machine_id", $request->machine_id)
                // ->where("used", ">", 1)
                // ->get();
                $operationOrders = OperationOrder::where('id', $operationOrderDetail->operation_order_id)->get();

                foreach ($operationOrders as $operationOrder) {
                    $used = MachineSupplie::where('machine_id', $operationOrder->machine_id)->whereIn('supplie_id', $machine_supplies_id)->pluck('used')->toArray();
                    $notes = $operationOrder->notes;
                    $empIds = explode(',', $operationOrder->employee_id);
                    $employeesNames = Employee::whereIn('id', $empIds)->pluck('name')->toArray();

                    $supplie_name = MachineSupplie::with('supplie')
                        ->where('machine_id', $operationOrder->machine_id)
                        ->whereIn('supplie_id', $machine_supplies_id)
                        ->get();

                    $supplieNames = $supplie_name->pluck('supplie.name')->toArray();

                    // Get supply quantities
                    $supplie_quantity_used = $operationOrderDetail->supplie_quantity_used;
                    $supplie_quantity_pre_used = $operationOrderDetail->supplie_quantity_pre_used;

                    $html .= '<h5 style="text-align: center;">بيانات أمر الشغل رقم  : ' . $operationOrder->id . '</h5>';
                    $html .= '<h5 style="text-align: center;">اسم العميل  : ' . $operationOrder->client_name??'' . '</h5>';
                    $html .= '<h5 style="display:inline-block;max-width:300px;min-width:100px;margin-left: 65px;">الآلة : ' . optional($operationOrder->machine)->name . '</h5>';
                    $html .= '<h5 style="display:inline-block;max-width:300px;min-width:100px;margin-left: 65px;">المستخدم : ' . optional($operationOrder->user)->name . '</h5>';
                    // $html .= '<h5 style="display:inline-block;max-width:300px;min-width:100px;margin-left: 65px;">مشرف : ' . optional($operationOrder->supervisor)->name . '</h5>';
                    $html .= '<h5 style="display:inline-block;max-width:350px;min-width:100px;margin-left: 65px;">الموظفين : ' . implode(" , ", $employeesNames) . '</h5>';
                    $html .= '<h5 style="display:inline-block;max-width:300px;min-width:100px;margin-left: 65px;">التاريخ : ' . $operationOrder->date . '</h5>';
                    $html .= '<br>';

                    // Display supplies table
                    if (!empty($supplieNames)) {
                        $html .= '<div style="margin: 20px 0;">';
                        $html .= '<h5 style="margin-bottom: 15px;">المستلزمات المستخدمة:</h5>';
                        $html .= '<table style="width: 100%; border-collapse: collapse; border: 1px solid #ddd;">';
                        $html .= '<thead>';
                        $html .= '<tr style="background-color: #f2f2f2;">';
                        $html .= '<th style="border: 1px solid #ddd; padding: 8px; text-align: center;">اسم المستلزم</th>';
                        $html .= '<th style="border: 1px solid #ddd; padding: 8px; text-align: center;">الكمية المستخدمة</th>';
                        $html .= '<th style="border: 1px solid #ddd; padding: 8px; text-align: center;">الكمية السابقة</th>';
                        $html .= '</tr>';
                        $html .= '</thead>';
                        $html .= '<tbody>';

                        // Split quantities if they are comma-separated
                        $used_quantities = $supplie_quantity_used;
                        $pre_used_quantities = explode(',', $supplie_quantity_pre_used);

                        foreach ($supplieNames as $index => $supplieName) {
                            $used_qty = isset($used_quantities) ? trim($used_quantities) : '0';
                            $pre_used_qty = isset($pre_used_quantities[$index]) ? trim($pre_used_quantities[$index]) : '0';

                            $html .= '<tr>';
                            $html .= '<td style="border: 1px solid #ddd; padding: 8px; text-align: center;">' . $supplieName . '</td>';
                            $html .= '<td style="border: 1px solid #ddd; padding: 8px; text-align: center;">' . $used_qty . '</td>';
                            $html .= '<td style="border: 1px solid #ddd; padding: 8px; text-align: center;">' . $pre_used_qty . '</td>';
                            $html .= '</tr>';
                        }

                        $html .= '</tbody>';
                        $html .= '</table>';
                        $html .= '</div>';
                    }
                    if ($operationOrder->out_operation == 0) {
                        $html .= '<h5 style="display:inline-block;max-width:350px;min-width:100px;margin-left: 65px;">اسم الخامة : ' . optional($operationOrderDetail->item)->name . '</h5>';
                        $itemName = DB::table('items')->where('id', $operationOrderDetail->out_item_id)->first();
                        $html .= '<h5 style="display:inline-block;max-width:300px;min-width:100px;margin-left: 65px;">اسم الناتج : ' . optional($itemName)->name . '</h5>';
                    } else {
                        $html .= '<h5 style="display:inline-block;max-width:350px;min-width:100px;margin-left: 65px;">اسم الخامة المستخدمة : ' . optional($operationOrderDetail)->item_name . '</h5>';
                        $html .= '<h5 style="display:inline-block;max-width:300px;min-width:100px;margin-left: 65px;">كمية الخامة المستخدمة : ' . optional($operationOrderDetail)->old_item_quantity . '</h5>';
                        $html .= '<h5 style="display:inline-block;max-width:300px;min-width:100px;margin-left: 65px;">الخامة الناتجة : ' . $operationOrderDetail->out_item_name . '</h5>';
                    }
                    // $html .= '<h5 style="display:inline-block;max-width:300px;min-width:100px;margin-left: 65px;">الطول : ' .$machineSupplies->Supplie['id'].'</h5>';
                    $html .= '<h5 style="display:inline-block;max-width:300px;min-width:100px;margin-left: 65px;">الطول : ' . $operationOrderDetail->length . '</h5>';
                    $html .= '<h5 style="display:inline-block;max-width:300px;min-width:100px;margin-left: 65px;">العرض : ' . $operationOrderDetail->width . '</h5>';
                    // $html .= '<h5 style="display:inline-block;max-width:300px;min-width:100px;margin-left: 65px;">السعر : '.$operationOrderDetail->price .'</h5>';
                    $html .= '<h5 style="display:inline-block;max-width:300px;min-width:100px;margin-left: 65px;">الكمية : ' . $operationOrderDetail->quantity . '</h5>';
                    $html .= '<h5 style="display:inline-block;max-width:100%;min-width:100%;margin-left: 65px;word-wrap: break-word;">ملاحظات : ' . $notes . '</h5>';



                    // if (auth()->user()->can('update_operation_orders')) {
                    //     $html .= '<a class="btn btn-warning" style="text-decoration: none;" target="_blank" href="' . route("dashboard.operation_orders.edit", $operationOrder->id) . '" ><i class="fa fa-edit"></i> Edit</a>';
                    // }
                }
            }
        }

        return response()->json(['html' => $html, 'used' => $used]);
    }
    // public function getOpertOrderInfo(Request $request)
    // {
    //     if (!$request->operation_order_detail_id) {
    //         $html = '';
    //         $html .= '<h5>برجاء اختيار امر التشغيل</h5>';
    //     } else {
    //         $html = '';
    //         $operationOrderDetails = OperationOrderDetail::where('id', $request->operation_order_detail_id)->get();
    //         foreach ($operationOrderDetails as $operationOrderDetail) {
    //             $machine_supplies_id = explode(',', $operationOrderDetail->operation_suplies_id);
    //             // $machineSupplies = MachineSupplie::where("machine_id", $request->machine_id)
    //             // ->where("used", ">", 1)
    //             // ->get();
    //             $operationOrders = OperationOrder::where('id', $operationOrderDetail->operation_order_id)->get();

    //             foreach ($operationOrders as $operationOrder) {
    //                 $used = MachineSupplie::where('machine_id', $operationOrder->machine_id)->whereIn('supplie_id', $machine_supplies_id)->pluck('used')->toArray();
    //                 $notes = $operationOrder->notes;
    //                 $empIds = explode(',', $operationOrder->employee_id);
    //                 $employeesNames = Employee::whereIn('id', $empIds)->pluck('name')->toArray();

    //                 $supplie_name = MachineSupplie::with('supplie')
    //                     ->where('machine_id', $operationOrder->machine_id)
    //                     ->whereIn('supplie_id', $machine_supplies_id)
    //                     ->get();

    //                 $supplieNames = $supplie_name->pluck('supplie.name')->toArray();

    //                 // dd($supplieNames);


    //                 $html .= '<h5 style="text-align: center;">بيانات أمر الشغل رقم  : ' . $operationOrder->id . '</h5>';
    //                 $html .= '<h5 style="display:inline-block;max-width:300px;min-width:100px;margin-left: 65px;">الآلة : ' . optional($operationOrder->machine)->name . '</h5>';
    //                 $html .= '<h5 style="display:inline-block;max-width:300px;min-width:100px;margin-left: 65px;">المستخدم : ' . optional($operationOrder->user)->name . '</h5>';
    //                 // $html .= '<h5 style="display:inline-block;max-width:300px;min-width:100px;margin-left: 65px;">مشرف : ' . optional($operationOrder->supervisor)->name . '</h5>';
    //                 $html .= '<h5 style="display:inline-block;max-width:350px;min-width:100px;margin-left: 65px;">الموظفين : ' . implode(" , ", $employeesNames) . '</h5>';
    //                 $html .= '<h5 style="display:inline-block;max-width:300px;min-width:100px;margin-left: 65px;">التاريخ : ' . $operationOrder->date . '</h5>';
    //                 $html .= '<br>';

    //                 if (!empty($supplieNames)) {
    //                     $html .= '<h5 style="display:inline-block;max-width:350px;min-width:100px;margin-left: 65px;"> المستلزم : ' . implode(" , ", $supplieNames) . '</h5>';
    //                 }
    //                 if ($operationOrder->out_operation == 0) {
    //                     $html .= '<h5 style="display:inline-block;max-width:350px;min-width:100px;margin-left: 65px;">اسم الخامة : ' . optional($operationOrderDetail->item)->name . '</h5>';
    //                     $itemName = DB::table('items')->where('id', $operationOrderDetail->out_item_id)->first();
    //                     $html .= '<h5 style="display:inline-block;max-width:300px;min-width:100px;margin-left: 65px;">اسم الناتج : ' . optional($itemName)->name . '</h5>';
    //                 } else {
    //                     $html .= '<h5 style="display:inline-block;max-width:350px;min-width:100px;margin-left: 65px;">اسم الخامة المستخدمة : ' . optional($operationOrderDetail)->item_name . '</h5>';
    //                     $html .= '<h5 style="display:inline-block;max-width:300px;min-width:100px;margin-left: 65px;">كمية الخامة المستخدمة : ' . optional($operationOrderDetail)->old_item_quantity . '</h5>';
    //                     $html .= '<h5 style="display:inline-block;max-width:300px;min-width:100px;margin-left: 65px;">الخامة الناتجة : ' . $operationOrderDetail->out_item_name . '</h5>';
    //                 }
    //                 // $html .= '<h5 style="display:inline-block;max-width:300px;min-width:100px;margin-left: 65px;">الطول : ' .$machineSupplies->Supplie['id'].'</h5>';
    //                 $html .= '<h5 style="display:inline-block;max-width:300px;min-width:100px;margin-left: 65px;">الطول : ' . $operationOrderDetail->length . '</h5>';
    //                 $html .= '<h5 style="display:inline-block;max-width:300px;min-width:100px;margin-left: 65px;">العرض : ' . $operationOrderDetail->width . '</h5>';
    //                 // $html .= '<h5 style="display:inline-block;max-width:300px;min-width:100px;margin-left: 65px;">السعر : '.$operationOrderDetail->price .'</h5>';
    //                 $html .= '<h5 style="display:inline-block;max-width:300px;min-width:100px;margin-left: 65px;">الكمية : ' . $operationOrderDetail->quantity . '</h5>';
    //                 $html .= '<h5 style="display:inline-block;max-width:100%;min-width:100%;margin-left: 65px;word-wrap: break-word;">ملاحظات : ' . $notes . '</h5>';
    //                 // if (auth()->user()->can('update_operation_orders')) {
    //                 //     $html .= '<a class="btn btn-warning" style="text-decoration: none;" target="_blank" href="' . route("dashboard.operation_orders.edit", $operationOrder->id) . '" ><i class="fa fa-edit"></i> Edit</a>';
    //                 // }
    //             }
    //         }
    //     }

    //     return response()->json(['html' => $html, 'used' => $used]);
    // }
    public function getOpertOrderWeight(Request $request)
    {
        if (isset($request->operation_order_detail_id) && isset($request->actual_output) && isset($request->thickness)) {
            $weight = '';
            $total_used_length = '';
            $operationOrderDetails = OperationOrderDetail::where('id', $request->operation_order_detail_id)->get();
            $operationOrderDetail = OperationOrderDetail::where('id', $request->operation_order_detail_id)->first();
            $operationOrder = OperationOrder::where('id', $operationOrderDetail->operation_order_id)->first();
            $total_used_length = $operationOrder->total_used_length;
            // dd($operationOrder);
            foreach ($operationOrderDetails as $operationOrderDetail) {
                $weight = 8 * ($operationOrderDetail->length) * ($operationOrderDetail->width) * ($request->actual_output) * ($request->thickness);
            }
            return response()->json(['weight' => $weight, 'total_used_length' => $total_used_length]);
        } else if (isset($request->operation_order_detail_id)) {
            $weight = '';
            $total_used_length = '';
            $operationOrderDetails = OperationOrderDetail::where('id', $request->operation_order_detail_id)->first();
            $operationOrder = OperationOrder::where('id', $operationOrderDetails->operation_order_id)->first();
            $total_used_length = $operationOrder->total_used_length;
            return response()->json(['weight' => $weight, 'total_used_length' => $total_used_length]);
        } else {
            $weight = '';
            $total_used_length = '';

            return response()->json(['weight' => $weight, 'total_used_length' => $total_used_length]);
        }
    }
    public function getDamageWeight(Request $request)
    {
        if (isset($request->operation_order_detail_id) && isset($request->damage_length) && isset($request->damage_quantity) && isset($request->thickness)) {
            $weight = '';
            $operationOrderDetails = OperationOrderDetail::where('id', $request->operation_order_detail_id)->get();
            // dd($operationOrder);
            foreach ($operationOrderDetails as $operationOrderDetail) {
                $weight = 8 * ($request->damage_length) * ($operationOrderDetail->width) * ($request->damage_quantity) * ($request->thickness);
            }
            return response()->json(['weight' => round($weight, 0)]);
            // return response()->json(['weight' => $weight]);
        } else {
            $weight = '';

            return response()->json(['weight' => $weight]);
        }
    }

    public function updateConfirm(Request $request)
    {
        // $counter = 0;
        $requestConfirm = 0;
        if (isset($request->resource)) {
            foreach ($request->resource as $res) {
                $resource = OperationOrderResult::where('id', $res['itemId'])->first();
                // dd($request->resource);
                if ($resource) {
                    if (array_key_exists('confirmed', $res)) {
                        if ($res['confirmed'] == 'on')
                            $res['confirmed'] = 1;
                        else
                            $res['confirmed'] = 0;

                        // $dt = new DateTime;
                        $resource->update([
                            'confirmed' => $res['confirmed'],
                            'confirm_notes' => $res['confirm_notes'],
                            'user_id' => auth()->user()->id,
                            'confirmed_at' => date('Y-m-d h:i:s'),
                        ]);
                    
                    	$operationOrder = OperationOrder::where('id', $resource->operation_order_id)->first();
                        $operationOrder->tracks()->where('step_name', 'production_manager')->update([
                            'status' => $res['confirmed'] == 1 ? 'approved' : 'rejected',
                            'user_id' => auth()->user()->id,
                            'notes' => $res['confirm_notes'] ?? null,
                            'action_at' => now(),
                        ]);
                    }

                    if (array_key_exists('store_confirm', $res)) {
                        if ($res['store_confirm'] == 'on')
                            $res['store_confirm'] = 1;
                        else
                            $res['store_confirm'] = 0;

                        $resource->update([
                            'store_confirm' => $res['store_confirm'],
                            'store_confirm_notes' => $res['store_confirm_notes'],
                        ]);

                        $operationOrder = OperationOrder::where('id', $resource->operation_order_id)->first();
                        $machine = Machines::where('id', $operationOrder->machine_id)->first();
                        $store = DB::table('stores')
                            ->select('id', 'name')
                            ->where('id', $machine->store_id)
                            ->first();
                        $orderDetail = OperationOrderDetail::where('id', $resource->order_details_id)->where('operation_order_id', $operationOrder->id)->first();
                        // return $orderDetail;
                        DB::beginTransaction();

                        //decrement old item quntity
                        if ($resource->old_item_quantity > 0) {
                            $quantity = Quantity::where('ownerable_type', 'App\Models\Store')
                                ->where('ownerable_id', $store->id)
                                ->where('item_id', $orderDetail->item_id)
                                ->first();


                            $quantity->decrement('quantity', $resource->old_item_quantity);
                            $quantity->save();

                            $orderDetail->new_in_balance = $quantity->quantity;
                            $orderDetail->save();
                        }
                        if ($resource->actual_output > 0) {
                            if (isset($orderDetail->out_item_id)) {
                                $quantity = Quantity::where('ownerable_type', 'App\Models\Store')
                                    ->where('ownerable_id', $store->id)
                                    ->where('item_id', $orderDetail->out_item_id)
                                    ->first();


                                $quantity->increment('quantity', $resource->actual_output);
                                $quantity->save();

                                $orderDetail->new_out_balance = $quantity->quantity;
                                $orderDetail->save();
                            } else {
                                if ($orderDetail->is_special == 1) {    //special
                                    $specail = Special::create([        //create in item as special
                                        'price' => $orderDetail->price,
                                        'name' => $orderDetail->out_name,
                                        'length' => $orderDetail->length,
                                        'width' => $orderDetail->width,
                                        'weight' => $resource->weight,
                                        'group_id' => $orderDetail->out_group_id,
                                        'is_special' => 1,
                                        'operat_ord_id' => $operationOrder->id,
                                    ]);
                                    $specail->code = $specail->id;
                                    $specail->save();

                                    $quantity = Quantity::create([                  //create in quantity
                                        'ownerable_type' => 'App\Models\Store',
                                        'ownerable_id' => $store->id,
                                        'item_id' => $specail->id,
                                        'quantity' => $resource->actual_output
                                    ]);
                                }
                            }
                        }

                        $orderResultDetails = OperationOrderResultDetail::where('order_results_id', $resource->id)->get();

                        foreach ($orderResultDetails as $orderResultDetail) {
                            if ($orderResultDetail->damage_quantity > 0) {
                                if (isset($orderResultDetail->old_damage_id)) {
                                    $quantity = Quantity::where('ownerable_type', 'App\Models\Store')
                                        ->where('ownerable_id', $store->id)
                                        ->where('item_id', $orderResultDetail->old_damage_id)
                                        ->first();
                                    if ($orderResultDetail->damage_type == 'scrap')
                                        $quantity->increment('quantity', $orderResultDetail->damage_weight);
                                    else
                                        $quantity->increment('quantity', $orderResultDetail->damage_quantity);

                                    $quantity->save();
                                } else {
                                    if ($orderResultDetail->damage_type == 'scrap')
                                        $damageGroup = DB::table('groups')->where('name', 'خرده')->first();
                                    else
                                        $damageGroup = DB::table('groups')->where('name', 'Damage')->first();

                                    $damage = Damage::create([        //create in item as damage
                                        'price' => $orderResultDetail->damage_price,
                                        'name' => $orderResultDetail->damage_name,
                                        'is_damage' => 1,
                                        'length' => $orderResultDetail->damage_length,
                                        'width' => $orderResultDetail->damage_width,
                                        'weight' => $orderResultDetail->damage_weight,
                                        'damage_type' => $orderResultDetail->damage_type,
                                        'group_id' => $damageGroup->id,
                                        'operat_ord_id' => $operationOrder->id,
                                    ]);

                                    $damage->code = $damage->id;
                                    $damage->save();

                                    $quantity = Quantity::create([                  //create in quantity
                                        'ownerable_type' => 'App\Models\Store',
                                        'ownerable_id' => $store->id,
                                        'item_id' => $damage->id,
                                        'quantity' => $orderResultDetail->damage_quantity
                                    ]);
                                    //decrement the damage quantity from old item quantity
                                }
                                $oldItemQuantity = Quantity::where('ownerable_type', 'App\Models\Store')
                                    ->where('ownerable_id', $store->id)
                                    ->where('item_id', $orderDetail->item_id)
                                    ->first();
                                $oldItemQuantity->decrement('quantity', $orderResultDetail->damage_weight);
                                $oldItemQuantity->save();
                            }
                        }
                    
                        $operationOrder->tracks()->where('step_name', 'store_manager')->update([
                            'status' => $res['store_confirm'] == 1 ? 'approved' : 'rejected',
                            'user_id' => auth()->user()->id,
                            'notes' => $res['store_confirm_notes'] ?? null,
                            'action_at' => now(),
                        ]);

                        DB::commit();
                    }

                    if (array_key_exists('confirm_notes', $res)) {
                        $resource->update([
                            'confirm_notes' => $res['confirm_notes'],
                        ]);
                    }

                    if (array_key_exists('store_confirm_notes', $res)) {
                        $resource->update([
                            'store_confirm_notes' => $res['store_confirm_notes'],
                        ]);
                    }

                    if (array_key_exists('store_employees', $res)) {
                        $employees = $res['store_employees'];
                        $resource->update([
                            'store_employees' => implode(',', $employees),
                        ]);
                    }
                }

                // $counter++;
            }
        } else {
            session()->flash('error', 'بالرجاء تحديد ناتج أمر شغل للتأكيد');
            return redirect()->back();
        }
        session()->flash('success', __('site.confirmed_successfully'));
        return redirect()->back();
    }
    //

    public function updateConfirmOut(Request $request)
    {
        // $counter = 0;
        $requestConfirm = 0;
        if (isset($request->resource)) {
            foreach ($request->resource as $res) {
                $resource = OperationOrderResult::where('id', $res['itemId'])->first();
                // dd($request->resource);
                if ($resource) {
                    if (array_key_exists('confirmed', $res)) {
                        if ($res['confirmed'] == 'on')
                            $res['confirmed'] = 1;
                        else
                            $res['confirmed'] = 0;

                        $resource->update([
                            'confirmed' => $res['confirmed'],
                            'confirm_notes' => $res['confirm_notes'],
                            'user_id' => auth()->user()->id,
                            'confirmed_at' => date('Y-m-d h:i:s'),
                        ]);

                        $operationOrder = OperationOrder::where('id', $resource->operation_order_id)->first();
                        $operationOrder->update([
                            'out_confirmed' => $res['confirmed'],
                            'confirm_notes' => $res['confirm_notes'],
                            'confirm_user_id' => auth()->user()->id,
                            'confirmed_at' => date('Y-m-d h:i:s'),
                        ]);
                    
                        $operationOrder->tracks()->where('step_name', 'production_manager')->update([
                            'status' => $res['confirmed'] == 1 ? 'approved' : 'rejected',
                            'user_id' => auth()->user()->id,
                            'notes' => $res['confirm_notes'] ?? null,
                            'action_at' => now(),
                        ]);
                    }

                    if (array_key_exists('store_confirm', $res)) {
                        if ($res['store_confirm'] == 'on')
                            $res['store_confirm'] = 1;
                        else
                            $res['store_confirm'] = 0;

                        $operationOrder = OperationOrder::where('id', $resource->operation_order_id)->first();
                        $machine = Machines::where('id', $operationOrder->machine_id)->first();
                        $store = DB::table('stores')
                            ->select('id', 'name')
                            ->where('id', $machine->store_id)
                            ->first();

                        $operationOrder = OperationOrder::where('id', $resource->operation_order_id)->first();
                        $orderResultDetails = OperationOrderResultDetail::where('order_results_id', $resource->id)->get();
                        DB::beginTransaction();

                        foreach ($orderResultDetails as $orderResultDetail) {
                            if ($orderResultDetail->damage_quantity > 0) {
                                if (isset($orderResultDetail->old_damage_id)) {
                                    $quantity = Quantity::where('ownerable_type', 'App\Models\Store')
                                        ->where('ownerable_id', $store->id)
                                        ->where('item_id', $orderResultDetail->old_damage_id)
                                        ->first();
                                    $quantity->increment('quantity', $orderResultDetail->damage_weight);
                                    $quantity->save();
                                } else {
                                    $damageGroup = DB::table('groups')->where('name', 'خرده')->first();
                                    $damage = Damage::create([        //create in item as damage
                                        'price' => $orderResultDetail->damage_price,
                                        'name' => $orderResultDetail->damage_name,
                                        'is_damage' => 1,
                                        'length' => $orderResultDetail->damage_length,
                                        'width' => $orderResultDetail->damage_width,
                                        'weight' => $orderResultDetail->damage_weight,
                                        'damage_type' => $orderResultDetail->damage_type,
                                        'group_id' => $damageGroup->id,
                                        'operat_ord_id' => $operationOrder->id,
                                    ]);

                                    $damage->code = $damage->id;
                                    $damage->save();

                                    $quantity = Quantity::create([
                                        'ownerable_type' => 'App\Models\Store',
                                        'ownerable_id' => $store->id,
                                        'item_id' => $damage->id,
                                        'quantity' => $orderResultDetail->damage_quantity
                                    ]);
                                    //decrement the damage quantity from old item quantity
                                }
                            }
                        }

                        $resource->update([
                            'store_confirm' => $res['store_confirm'],
                            'store_confirm_notes' => $res['store_confirm_notes'],
                        ]);
                    
                        $operationOrder->tracks()->where('step_name', 'store_manager')->update([
                            'status' => $res['store_confirm'] == 1 ? 'approved' : 'rejected',
                            'user_id' => auth()->user()->id,
                            'notes' => $res['store_confirm_notes'] ?? null,
                            'action_at' => now(),
                        ]);

                        DB::commit();
                    }

                    if (array_key_exists('confirm_notes', $res)) {
                        $resource->update([
                            'confirm_notes' => $res['confirm_notes'],
                        ]);
                    }

                    if (array_key_exists('store_confirm_notes', $res)) {
                        $resource->update([
                            'store_confirm_notes' => $res['store_confirm_notes'],
                        ]);
                    }

                    if (array_key_exists('store_employees', $res)) {
                        $employees = $res['store_employees'];
                        $resource->update([
                            'store_employees' => implode(',', $employees),
                        ]);
                    }
                }

                // $counter++;
            }
        } else {
            session()->flash('error', 'بالرجاء تحديد ناتج أمر شغل للتأكيد');
            return redirect()->back();
        }
        session()->flash('success', __('site.confirmed_successfully'));
        return redirect()->back();
    }
    //

    // public function updateConfirm(Request $request)
    // {
    //     // dd($request->all());
    //     // $counter = 0;
    //     $requestConfirm = 0;
    //     foreach ($request->resource as $res) {
    //         $resource = OperationOrderResult::where('id', $res['itemId'])->first();
    //         // dd($request->resource);
    //         if ($resource) {
    //             if (array_key_exists('confirmed', $res)) {
    //                 if ($res['confirmed'] == 'on')
    //                     $res['confirmed'] = 1;
    //                 else
    //                     $res['confirmed'] = 0;

    //                 // $dt = new DateTime;
    //                 $resource->update([
    //                     'confirmed' => $res['confirmed'],
    //                     'confirm_notes' => $res['confirm_notes'],
    //                     'user_id' => auth()->user()->id,
    //                     'confirmed_at' => date('Y-m-d h:i:s'),
    //                 ]);

    //                 $operationOrder = OperationOrder::where('id', $resource->operation_order_id)->first();
    //                 $machine = Machines::where('id', $operationOrder->machine_id)->first();
    //                 $store = DB::table('stores')
    //                     ->select('id', 'name')
    //                     ->where('id', $machine->store_id)
    //                     ->first();
    //                 $orderDetail = OperationOrderDetail::where('id', $resource->order_details_id)->where('operation_order_id', $operationOrder->id)->first();

    //                 DB::beginTransaction();

    //                 //decrement old item quntity
    //                 if ($resource->old_item_quantity > 0) {
    //                     $quantity = Quantity::where('ownerable_type', 'App\Models\Store')
    //                         ->where('ownerable_id', $store->id)
    //                         ->where('item_id', $orderDetail->item_id)
    //                         ->first();


    //                     $quantity->decrement('quantity', $resource->old_item_quantity);
    //                     $quantity->save();

    //                     $orderDetail->new_in_balance = $quantity->quantity;
    //                     $orderDetail->save();
    //                 }
    //                 if ($resource->actual_output > 0) {
    //                     if (isset($orderDetail->out_item_id)) {
    //                         $quantity = Quantity::where('ownerable_type', 'App\Models\Store')
    //                             ->where('ownerable_id', $store->id)
    //                             ->where('item_id', $orderDetail->out_item_id)
    //                             ->first();


    //                         $quantity->increment('quantity', $resource->actual_output);
    //                         $quantity->save();

    //                         $orderDetail->new_out_balance = $quantity->quantity;
    //                         $orderDetail->save();
    //                     } else {
    //                         if ($orderDetail->is_special == 1) {    //special
    //                             $specail = Special::create([        //create in item as special
    //                                 'price' => $orderDetail->price,
    //                                 'name' => $orderDetail->out_name,
    //                                 'length' => $orderDetail->length,
    //                                 'width' => $orderDetail->width,
    //                                 'weight' => $resource->weight,
    //                                 'group_id' => $orderDetail->out_group_id,
    //                                 'is_special' => 1,
    //                                 'operat_ord_id' => $operationOrder->id,
    //                             ]);
    //                             $specail->code = $specail->id;
    //                             $specail->save();

    //                             $quantity = Quantity::create([                  //create in quantity
    //                                 'ownerable_type'    => 'App\Models\Store',
    //                                 'ownerable_id' => $store->id,
    //                                 'item_id' => $specail->id,
    //                                 'quantity' => $resource->actual_output
    //                             ]);
    //                         }
    //                     }
    //                 }

    //                 $orderResultDetails = OperationOrderResultDetail::where('order_results_id', $resource->id)->get();

    //                 foreach ($orderResultDetails as $orderResultDetail) {
    //                     if ($orderResultDetail->damage_quantity > 0) {
    //                         if (isset($orderResultDetail->old_damage_id)) {
    //                             $quantity = Quantity::where('ownerable_type', 'App\Models\Store')
    //                                 ->where('ownerable_id', $store->id)
    //                                 ->where('item_id', $orderResultDetail->old_damage_id)
    //                                 ->first();
    //                             $quantity->increment('quantity', $orderResultDetail->damage_quantity);
    //                             $quantity->save();
    //                         } else {
    //                             $damageGroup = DB::table('groups')->where('name', 'Damage')->first();

    //                             $damage = Damage::create([        //create in item as damage
    //                                 'price' => $orderResultDetail->damage_price,
    //                                 'name' => $orderResultDetail->damage_name,
    //                                 'is_damage' => 1,
    //                                 'length' => $orderResultDetail->damage_length,
    //                                 'width' => $orderResultDetail->damage_width,
    //                                 'weight' => $orderResultDetail->damage_weight,
    //                                 'damage_type' => $orderResultDetail->damage_type,
    //                                 'group_id' => $damageGroup->id,
    //                                 'operat_ord_id' => $operationOrder->id,
    //                             ]);

    //                             $damage->code = $damage->id;
    //                             $damage->save();

    //                             $quantity = Quantity::create([                  //create in quantity
    //                                 'ownerable_type'    => 'App\Models\Store',
    //                                 'ownerable_id' => $store->id,
    //                                 'item_id' => $damage->id,
    //                                 'quantity' => $orderResultDetail->damage_quantity
    //                             ]);

    //                             //decrement the damage quantity from old item quantity
    //                             $oldItemQuantity = Quantity::where('ownerable_type', 'App\Models\Store')
    //                                 ->where('ownerable_id', $store->id)
    //                                 ->where('item_id', $orderDetail->item_id)
    //                                 ->first();
    //                             $oldItemQuantity->decrement('quantity', $orderResultDetail->damage_weight);
    //                             $oldItemQuantity->save();
    //                         }
    //                     }
    //                 }

    //                 DB::commit();
    //             }
    //             if (array_key_exists('confirm_notes', $res)) {
    //                 // $dt = new DateTime;
    //                 $resource->update([
    //                     'confirm_notes' => $res['confirm_notes'],
    //                 ]);
    //             }
    //         }

    //         // $counter++;
    //     }
    //     session()->flash('success', __('site.confirmed_successfully'));
    //     return redirect()->back();
    // }

    // public function updateConfirmOut(Request $request)
    // {
    //     // dd($request->all());
    //     // $counter = 0;
    //     $requestConfirm = 0;
    //     foreach ($request->resource as $res) {
    //         $resource = OperationOrderResult::where('id', $res['itemId'])->first();
    //         // dd($request->resource);
    //         if ($resource) {
    //             if (array_key_exists('confirmed', $res)) {
    //                 if ($res['confirmed'] == 'on')
    //                     $res['confirmed'] = 1;
    //                 else
    //                     $res['confirmed'] = 0;

    //                 // $dt = new DateTime;


    //                 $operationOrder = OperationOrder::where('id', $resource->operation_order_id)->first();

    //                 DB::beginTransaction();

    //                 $resource->update([
    //                     'confirmed' => $res['confirmed'],
    //                     'confirm_notes' => $res['confirm_notes'],
    //                     'user_id' => auth()->user()->id,
    //                     'confirmed_at' => date('Y-m-d h:i:s'),
    //                 ]);

    //                 $operationOrder->update([
    //                     'out_confirmed' => $res['confirmed'],
    //                     'confirm_notes' => $res['confirm_notes'],
    //                     'confirm_user_id' => auth()->user()->id,
    //                     'confirmed_at' => date('Y-m-d h:i:s'),
    //                 ]);

    //                 DB::commit();
    //             }
    //             if (array_key_exists('confirm_notes', $res)) {
    //                 // $dt = new DateTime;
    //                 $resource->update([
    //                     'confirm_notes' => $res['confirm_notes'],
    //                 ]);
    //                 // $operationOrder->update([
    //                 //     'confirm_notes' => $res['confirm_notes'],
    //                 // ]);
    //             }
    //         }

    //         // $counter++;
    //     }
    //     session()->flash('success', __('site.confirmed_successfully'));
    //     return redirect()->back();
    // }

    public function push_notification($message)
    {
        $options = array(
            'cluster' => 'eu',
            'useTLS' => true
        );
        $pusher = new Pusher(
            'e75d58425f4b10f93cfb',
            '49edd2fdb43527c84354',
            '417914',
            $options
        );
        $data['message'] = $message;
        $pusher->trigger('my-channel', 'my-event', $data);
        return true;
    }

    public function auto_complete_first(Request $request)
    {
        $term = $request->term;
        $results = [];

        if ($term) {
            $results = Damage::where('name', 'LIKE', '%' . $term . '%')
                ->where('is_damage', 1)->where('group_id', 63)
                ->pluck('name'); // Replace with your actual column name
        }

        return response()->json($results);
    }
    public function auto_complete_second(Request $request)
    {
        $term = $request->term;
        $results = [];

        if ($term) {
            $results = Damage::where('name', 'LIKE', '%' . $term . '%')
                ->where('is_damage', 1)->where('group_id', 71)
                ->pluck('name'); // Replace with your actual column name
        }


        return response()->json($results);
    }
}
//------------------------------------------------------------------------------------------------- ~Mohamed Maher~ -------------------------------------------------------------------------------------------------------------------------//
// use Storage;
// use App\Item;
// use App\User;
// use App\Admin;
// use App\Damage;
// use App\Special;
// use App\Student;
// use App\Employee;
// use App\Machines;
// use App\Quantity;
// use App\Reposite;
// use App\Supplies;
// use Pusher\Pusher;
// use App\Department;
// use App\SupplieTypes;
// use App\MachineSupplie;
// use App\OperationOrder;
// use Illuminate\Support\Arr;
// use Illuminate\Http\Request;
// use App\OperationOrderDetail;
// use App\OperationOrderResult;
// use Illuminate\Validation\Rule;
// use App\TrackingMachineSupplies;
// use Illuminate\Support\Facades\DB;
// use App\OperationOrderResultDetail;
// use Illuminate\Support\Facades\Log;
// use App\Http\Controllers\Controller;
// use Maatwebsite\Excel\Facades\Excel;
// use Yajra\DataTables\Facades\DataTables as FacadesDataTables;

// class OperationOrderResultController extends Controller
// {
//     public function __construct()
//     {
//         $this->middleware(['ability:admin,reade_operation_order_results'])->only('index');
//         $this->middleware(['ability:admin,create_operation_order_results'])->only('create');
//         $this->middleware(['ability:admin,update_operation_order_results'])->only('edit');
//         $this->middleware(['ability:admin,delete_operation_order_results'])->only('destroy');
//     }

//     public function getData(Request $request)
//     {
//         $query = OperationOrderResult::query();
//         if (isset($request['type']) && !empty($request['type'])) {
//             // if (auth()->user()->hasRole('store_factor_response') || auth()->user()->hasRole('admin')) {
//             //     $query->where('store_confirm', 0);
//             // } else {
//             //     $query->where('confirmed', 0);
//             // }
            
//             if (auth()->user()->hasRole('admin') || auth()->user()->hasRole('admin2') || auth()->user()->hasRole('store_factor_response') || auth()->user()->hasRole('safe_factor_response') || auth()->user()->hasRole('branch_factor_respons') || auth()->user()->hasRole('factor_response')) {
//                 $query->where('store_confirm', 0);
//             } else {
//                 $query->where('confirmed', 0);
//             }
//         }
//         // if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('مسئول فرع و مصنع' || auth()->user()->hasRole('مسئول مصنع') || auth()->user()->hasRole('مسئول مخزن و مصنع'))){
//         // if (auth()->user()->hasRole('admin') || auth()->user()->hasRole('branch_factor_respons') || auth()->user()->hasRole('store_factor_response') || auth()->user()->hasRole('factor_response')) {
//         //     $query->whereHas('operationOrder', function ($q) {
//         //         $q->where('out_operation', 0);
//         //     })
//         //         ->with(['operationOrder' => function ($q) {
//         //             $q->with('machine');
//         //         }])
//         //         ->get();
//         // } else {
//         //     $query = OperationOrderResult::toUser()->whereHas('operationOrder', function ($q) {
//         //         $q->where('out_operation', 0);
//         //     })->with(['operationOrder' => function ($q) {
//         //         $q->with('machine');
//         //     }]);
//         // }
        
//         if (auth()->user()->hasRole('admin') || auth()->user()->hasRole('admin2') || auth()->user()->hasRole('safe_factor_response') || auth()->user()->hasRole('branch_factor_respons') || auth()->user()->hasRole('store_factor_response') || auth()->user()->hasRole('factor_response')) {
//             $query->whereHas('operationOrder', function ($q) {
//                 $q->where('out_operation', 0);
//             })
//                 ->with(['operationOrder' => function ($q) {
//                     $q->with('machine');
//                 }])
//                 ->get();
//         } else if (auth()->user()->hasRole('branch_factor_respons') || auth()->user()->hasRole('safe_factor_response') || auth()->user()->hasRole('factor_response')) {
//             $query->whereHas('operationOrder', function ($q) {
//                 $q->where('out_operation', 0)->where('supervisor_process', auth()->user()->id);
//             })
//                 ->with(['operationOrder' => function ($q) {
//                     $q->with('machine');
//                 }])
//                 ->get();
//         } else if (auth()->user()->hasRole('store_factor_response')) {
//             $query->whereHas('operationOrder', function ($q) {
//                 $q->where('out_operation', 0)->where('supervisor_store', auth()->user()->id);
//             })
//                 ->with(['operationOrder' => function ($q) {
//                     $q->with('machine');
//                 }])
//                 ->get();
//         } else {
//             $query = OperationOrderResult::toUser()->whereHas('operationOrder', function ($q) {
//                 $q->where('out_operation', 0);
//             })->with(['operationOrder' => function ($q) {
//                 $q->with('machine');
//             }]);
//         }

//         return FacadesDataTables::eloquent($query->with(['operationOrder', 'operationOrder.supervisor', 'user'])->latest())
//             ->addColumn('action', function (OperationOrderResult $operationOrderResult) {
//                 $type = "action";
//                 return view("dashboard.operation_order_results.action", compact("operationOrderResult", "type"));
//             })
//             ->addColumn('confirm_notes', function (OperationOrderResult $operationOrderResult) {
//                 $type = "confirm_notes";
//                 return view("dashboard.operation_order_results.action", compact("operationOrderResult", "type"));
//             })

//             ->editColumn('operation_order_id', function (OperationOrderResult $operationOrderResult) {
//                 return '<a style="text-decoration: none;" target="_blank" href="' . route("dashboard.operation_orders.show", $operationOrderResult->operationOrder->id) . '" >' . $operationOrderResult->operationOrder->id . '</a>';
//             })
//             ->editColumn('date', function (OperationOrderResult $operationOrderResult) {
//                 return optional($operationOrderResult->operationOrder)->date;
//             })
//             ->editColumn('supervisor_name', function (OperationOrderResult $operationOrderResult) {
//                 return optional(optional($operationOrderResult->operationOrder)->supervisor)->name;
//             })
//             ->addColumn('machine_name', function (OperationOrderResult $operationOrderResult) {
//                 return optional(optional($operationOrderResult->operationOrder)->machine)->name;
//             })
//             ->addColumn('supervisor_process', function (OperationOrderResult $operationOrderResult) {
//                 if ($operationOrderResult->operationOrder->supervisor_process && $operationOrderResult->confirmed == 1) {
//                     $name_id = explode(',', $operationOrderResult->operationOrder->supervisor_process);
//                     $names = User::whereIn('id', $name_id)->pluck('name')->toArray();
//                     return implode(", ", $names);
//                 }
//                 return '';
//             })
//             ->addColumn('suplies_name', function (OperationOrderResult $operationOrderResult) {
//                 $operationSupliesIds = explode(',', $operationOrderResult->operationOrderDetail->operation_suplies_id);
//                 $operationSupliesNames = Supplies::whereIn('id', $operationSupliesIds)->pluck('name')->toArray();
//                 return $operationSupliesNames;
//             })

//             ->editColumn('user_id', function (OperationOrderResult $operationOrderResult) {
//                 if ($operationOrderResult->operationOrder->user_id) {
//                     $name = optional($operationOrderResult->operationOrder->user)->name;
//                     return $name;
//                 }
//                 return '';
//             })
            
//             ->addColumn('item', function (OperationOrderResult $operationOrderResult) {
//                 if ($operationOrderResult->operationOrderDetail->item) {
//                     $name = optional($operationOrderResult->operationOrderDetail->item)->name;
//                     return $name;
//                 }
//                 return '';
//             })
//             ->addColumn('old_item_quantity', function (OperationOrderResult $operationOrderResult) {
//                 if ($operationOrderResult->old_item_quantity) {
//                     $name = optional($operationOrderResult)->old_item_quantity;
//                     return $name;
//                 }
//                 return '';
//             })

//             // ->editColumn('user_id', function (OperationOrderResult $operationOrderResult) {
//             //     if ($operationOrderResult->user_id) {
//             //         $name = optional($operationOrderResult->user)->name;

//             //         return ' ' . $name . '<br>' . $operationOrderResult->confirmed_at;
//             //     }
//             //     return '';
//             // })
//             ->editColumn('employee_id', function (OperationOrderResult $operationOrderResult) {
//                 $empIds = explode(',', $operationOrderResult->employee_id);
//                 $employeesNames = Employee::whereIn('id', $empIds)->pluck('name')->toArray();
//                 return $employeesNames;
//             })
//             ->rawColumns(['action', 'confirm_notes', 'date', 'user_id', 'operation_order_id', 'supervisor_name', 'suplies_name'])
//             ->toJson();
//     }
//     //

//     public function getDataOut(Request $request)
//     {
//         $query = OperationOrderResult::query();
//         if (isset($request['type']) && !empty($request['type'])) {
//             // if (auth()->user()->hasRole('store_factor_response') || auth()->user()->hasRole('admin')) {
//             //     $query->where('store_confirm', 0);
//             // } else {
//             //     $query->where('confirmed', 0);
//             // }
            
//             if (auth()->user()->hasRole('admin') || auth()->user()->hasRole('admin2') || auth()->user()->hasRole('store_factor_response') || auth()->user()->hasRole('safe_factor_response') || auth()->user()->hasRole('branch_factor_respons') || auth()->user()->hasRole('factor_response')) {
//                 $query->where('store_confirm', 0);
//             } else {
//                 $query->where('confirmed', 0);
//             }
//         }
//         // if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('مسئول فرع و مصنع' || auth()->user()->hasRole('مسئول مصنع') || auth()->user()->hasRole('مسئول مخزن و مصنع'))){
//         // if (auth()->user()->hasRole('admin') || auth()->user()->hasRole('branch_factor_respons') || auth()->user()->hasRole('store_factor_response') || auth()->user()->hasRole('factor_response')) {
//         //     $query->whereHas('operationOrder', function ($q) {
//         //         $q->where('out_operation', 1);
//         //     })->get();
//         // } else {
//         //     $query = OperationOrderResult::toUser()->whereHas('operationOrder', function ($q) {
//         //         $q->where('out_operation', 1);
//         //     });
//         // }
        
//          if (auth()->user()->hasRole('admin') || auth()->user()->hasRole('admin2') || auth()->user()->hasRole('safe_factor_response') || auth()->user()->hasRole('branch_factor_respons') || auth()->user()->hasRole('store_factor_response') || auth()->user()->hasRole('factor_response')) {
//             $query->whereHas('operationOrder', function ($q) {
//                 $q->where('out_operation', 1);
//             })
//                 ->with(['operationOrder' => function ($q) {
//                     $q->with('machine');
//                 }])
//                 ->get();
//         } else if (auth()->user()->hasRole('branch_factor_respons') || auth()->user()->hasRole('safe_factor_response') || auth()->user()->hasRole('factor_response')) {
//             $query->whereHas('operationOrder', function ($q) {
//                 $q->where('out_operation', 1)->where('supervisor_process', auth()->user()->id);
//             })
//                 ->with(['operationOrder' => function ($q) {
//                     $q->with('machine');
//                 }])
//                 ->get();
//         } else if (auth()->user()->hasRole('store_factor_response')) {
//             $query->whereHas('operationOrder', function ($q) {
//                 $q->where('out_operation', 1)->where('supervisor_store', auth()->user()->id);
//             })
//                 ->with(['operationOrder' => function ($q) {
//                     $q->with('machine');
//                 }])
//                 ->get();
//         } else {
//             $query = OperationOrderResult::toUser()->whereHas('operationOrder', function ($q) {
//                 $q->where('out_operation', 1);
//             })->with(['operationOrder' => function ($q) {
//                 $q->with('machine');
//             }]);
//         }


//         return FacadesDataTables::eloquent($query->with(['operationOrder', 'operationOrder.supervisor', 'user'])->latest())
//             ->addColumn('action', function (OperationOrderResult $operationOrderResult) {
//                 $type = "action";
//                 return view("dashboard.operation_order_results.action_out", compact("operationOrderResult", "type"));
//             })
//             ->addColumn('confirm_notes', function (OperationOrderResult $operationOrderResult) {
//                 $type = "confirm_notes";
//                 return view("dashboard.operation_order_results.action_out", compact("operationOrderResult", "type"));
//             })
//             ->editColumn('operation_order_id', function (OperationOrderResult $operationOrderResult) {
//                 return '<a style="text-decoration: none;" target="_blank" href="' . route("dashboard.operation_orders.show", $operationOrderResult->operationOrder->id) . '" >' . $operationOrderResult->operationOrder->id . '</a>';
//             })
//             ->editColumn('date', function (OperationOrderResult $operationOrderResult) {
//                 return optional($operationOrderResult->operationOrder)->date;
//             })
//             ->editColumn('supervisor_name', function (OperationOrderResult $operationOrderResult) {
//                 return optional(optional($operationOrderResult->operationOrder)->supervisor)->name;
//             })
//             ->addColumn('machine_name', function (OperationOrderResult $operationOrderResult) {
//                 return optional(optional($operationOrderResult->operationOrder)->machine)->name;
//             })
//             ->addColumn('client_name', function (OperationOrderResult $operationOrderResult) {
//                 return optional(optional($operationOrderResult->operationOrder))->client_name;
//             })
//             ->addColumn('suplies_name', function (OperationOrderResult $operationOrderResult) {
//                 $operationSupliesIds = explode(',', $operationOrderResult->operationOrderDetail->operation_suplies_id);
//                 $operationSupliesNames = Supplies::whereIn('id', $operationSupliesIds)->pluck('name')->toArray();
//                 return $operationSupliesNames;
//             })

//             ->addColumn('supervisor_process', function (OperationOrderResult $operationOrderResult) {
//                 if ($operationOrderResult->operationOrder->supervisor_process && $operationOrderResult->confirmed == 1) {
//                     $name_id = explode(',', $operationOrderResult->operationOrder->supervisor_process);
//                     $names = User::whereIn('id', $name_id)->pluck('name')->toArray();
//                     return implode(", ", $names);
//                 }
//                 return '';
//             })
//             ->editColumn('user_id', function (OperationOrderResult $operationOrderResult) {
//                 if ($operationOrderResult->operationOrder->user_id) {
//                     $name = optional($operationOrderResult->operationOrder->user)->name;
//                     return $name;
//                 }
//                 return '';
//             })
//             ->addColumn('item', function (OperationOrderResult $operationOrderResult) {
//                 if ($operationOrderResult->operationOrderDetail->item_name) {
//                     $name = optional($operationOrderResult->operationOrderDetail)->item_name;
//                     return $name;
//                 }
//                 return '';
//             })
//             ->addColumn('old_item_quantity', function (OperationOrderResult $operationOrderResult) {
//                 if ($operationOrderResult->old_item_quantity) {
//                     $name = optional($operationOrderResult)->old_item_quantity;
//                     return $name;
//                 }
//                 return '';
//             })
//             // ->editColumn('user_id', function (OperationOrderResult $operationOrderResult) {
//             //     if ($operationOrderResult->user_id) {
//             //         $name = optional($operationOrderResult->user)->name;

//             //         return ' ' . $name . '<br>' . $operationOrderResult->confirmed_at;
//             //     }
//             //     return '';
//             // })
//             ->editColumn('employee_id', function (OperationOrderResult $operationOrderResult) {
//                 $empIds = explode(',', $operationOrderResult->employee_id);
//                 $employeesNames = Employee::whereIn('id', $empIds)->pluck('name')->toArray();
//                 return $employeesNames;
//             })
//             ->rawColumns(['action', 'confirm_notes', 'date', 'user_id', 'operation_order_id', 'supervisor_name', 'suplies_name'])
//             ->toJson();
//     }
//     //

//     public function index(Request $request)
//     {
//         if (isset($request['type']) && !empty($request['type'])) {
//             return view('dashboard.operation_order_results.index2');
//         }
//         return view('dashboard.operation_order_results.index');
//     }

//     public function indexOut(Request $request)
//     {
//         if (isset($request['type']) && !empty($request['type'])) {
//             return view('dashboard.operation_order_results.index_out2');
//         }
//         return view('dashboard.operation_order_results.index_out');
//     }

//     public function show(OperationOrderResult $operationOrderResult)
//     {
//         $operationSupliesIds = explode(',', $operationOrderResult->operationOrderDetail->operation_suplies_id);
        
//         $supplies = [];
//         $used = [];
//         $machine_id = $operationOrderResult->operationOrder->machine_id;
//         foreach ($operationSupliesIds as $ids) {
//             $supplies[] = Supplies::where('id', $ids)->first()->name;
//             $used[] = MachineSupplie::select('used')
//                 ->where('machine_id', $machine_id)
//                 ->where('supplie_id', $ids)->first()->used;
//         }
//         // $supplies = Supplies::whereIn('id', $operationSupliesIds)->pluck('name')->toArray();
//         // $machine_id = $operationOrderResult->operationOrder->machine_id;
//         // $used = MachineSupplie::select('used')
//         //     ->where('machine_id', $machine_id)
//         //     ->whereIn('supplie_id', $operationSupliesIds)->pluck('used');

//         $quantity = $operationOrderResult->operationOrderDetail->supplie_quantity_used;
//         // $quantity = $operationOrderResult->total_used_length;
//         return view('dashboard.operation_order_results.show', compact('operationOrderResult', 'supplies', 'quantity', 'used'));
//     }
    
//     public function showStore(OperationOrderResult $operationOrderResult)
//     {
//         $operationSupliesIds = explode(',', $operationOrderResult->operationOrderDetail->operation_suplies_id);
        
//         $supplies = [];
//         $used = [];
//         $machine_id = $operationOrderResult->operationOrder->machine_id;
//         foreach ($operationSupliesIds as $ids) {
//             $supplies[] = Supplies::where('id', $ids)->first()->name;
//             $used[] = MachineSupplie::select('used')
//                 ->where('machine_id', $machine_id)
//                 ->where('supplie_id', $ids)->first()->used;
//         }
//         // $supplies = Supplies::whereIn('id', $operationSupliesIds)->pluck('name')->toArray();
//         // $machine_id = $operationOrderResult->operationOrder->machine_id;
//         // $used = MachineSupplie::select('used')
//         //     ->where('machine_id', $machine_id)
//         //     ->whereIn('supplie_id', $operationSupliesIds)->pluck('used');

//         $quantity = $operationOrderResult->operationOrderDetail->supplie_quantity_used;
//         // $quantity = $operationOrderResult->total_used_length;
//         return view('dashboard.operation_order_results.showStore', compact('operationOrderResult', 'supplies', 'quantity', 'used'));
//     }
//     //

//     public function showOut(OperationOrderResult $operationOrderResult)
//     {
//         $operationSupliesIds = explode(',', $operationOrderResult->operationOrderDetail->operation_suplies_id);
        
//         $supplies = [];
//         $used = [];
//         $machine_id = $operationOrderResult->operationOrder->machine_id;
//         foreach ($operationSupliesIds as $ids) {
//             $supplies[] = Supplies::where('id', $ids)->first()->name;
//             $used[] = MachineSupplie::select('used')
//                 ->where('machine_id', $machine_id)
//                 ->where('supplie_id', $ids)->first()->used;
//         }
//         // $supplies = Supplies::whereIn('id', $operationSupliesIds)->pluck('name')->toArray();
//         // $machine_id = $operationOrderResult->operationOrder->machine_id;
//         // $used = MachineSupplie::select('used')
//         //     ->where('machine_id', $machine_id)
//         //     ->whereIn('supplie_id', $operationSupliesIds)->pluck('used');

//         $quantity = $operationOrderResult->operationOrderDetail->supplie_quantity_used;
//         // $quantity = optional($operationOrderResult->operationOrder)->total_used_length;
        
//         $client = $operationOrderResult->operationOrder->client_name;
//         return view('dashboard.operation_order_results.show_out', compact('operationOrderResult', 'supplies', 'quantity', 'client', 'used'));
//     }

//     public function showOutStore(OperationOrderResult $operationOrderResult)
//     {
//         $operationSupliesIds = explode(',', $operationOrderResult->operationOrderDetail->operation_suplies_id);
        
//         $supplies = [];
//         $used = [];
//         $machine_id = $operationOrderResult->operationOrder->machine_id;
//         foreach ($operationSupliesIds as $ids) {
//             $supplies[] = Supplies::where('id', $ids)->first()->name;
//             $used[] = MachineSupplie::select('used')
//                 ->where('machine_id', $machine_id)
//                 ->where('supplie_id', $ids)->first()->used;
//         }
//         // $supplies = Supplies::whereIn('id', $operationSupliesIds)->pluck('name')->toArray();
//         // $machine_id = $operationOrderResult->operationOrder->machine_id;
//         // $used = MachineSupplie::select('used')
//         //     ->where('machine_id', $machine_id)
//         //     ->whereIn('supplie_id', $operationSupliesIds)->pluck('used');

//         $quantity = $operationOrderResult->operationOrderDetail->supplie_quantity_used;
//         // $quantity = optional($operationOrderResult->operationOrder)->total_used_length;
        
//         $client = $operationOrderResult->operationOrder->client_name;
//         return view('dashboard.operation_order_results.showOutStore', compact('operationOrderResult', 'supplies', 'quantity', 'client', 'used'));
//     }
    
//     public function del_edit($operation_id)
//     {
//         //We have operation order id
//         $operation_order_detail = OperationOrderDetail::find($operation_id);
//         $operation_order = OperationOrder::find($operation_order_detail->operation_order_id);

//         $operation_order->update([
//             'machine_access' => 0,
//             'machine_edit' => 1
//         ]);
//         $operation_order_detail->update([
//             'active' => 0,
//         ]);

//         session()->flash('success', 'تم التعديل بنجاح');
//         return redirect()->back();
//     }
//     //

//     // public function show(OperationOrderResult $operationOrderResult)
//     // {

//     //     return view('dashboard.operation_order_results.show', compact('operationOrderResult'));
//     // }

//     // public function showOut(OperationOrderResult $operationOrderResult)
//     // {

//     //     return view('dashboard.operation_order_results.show_out', compact('operationOrderResult'));
//     // }

//     public function create()
//     {
//       if (auth()->user()->hasRole('admin') || auth()->user()->hasRole('safe_factor_response') || auth()->user()->hasRole('branch_factor_respons') || auth()->user()->hasRole('store_factor_response') || auth()->user()->hasRole('factor_response')) {
//             $operationOrders = OperationOrder::where('out_operation', 0)->latest()->get();
//             $operationOrdrDetails = OperationOrderDetail::whereHas('operationOrder', function ($q) {
//                 $q->where('out_operation', 0);
//             })->whereDoesntHave('operationOrderResults')->latest()->get();
//         } else {
//             $operationOrders = OperationOrder::toUser()->where('out_operation', 0)->latest()->get();
//             $operationOrdersIds = $operationOrders->pluck('id')->toArray();
//             $operationOrdrDetails = OperationOrderDetail::whereIn('operation_order_id', $operationOrdersIds)->whereDoesntHave('operationOrderResults')->latest()->get();
//         }
//         // dd($operationOrders);
//         $damages = Damage::where('is_damage', 1)->where('group_id', 63)->latest()->get();
//         $scrap = Damage::where('is_damage', 1)->where('group_id', 71)->latest()->get();
//         $employees = Employee::where('branch_id',auth()->user()->branch_id)->where('job_id',2)->where('active', 1)->latest()->get();

//         return view('dashboard.operation_order_results.create', compact('operationOrders', 'operationOrdrDetails', 'scrap','damages','employees'));
//     }

//     public function createOut()
//     {
//         if (auth()->user()->hasRole('admin') || auth()->user()->hasRole('safe_factor_response') || auth()->user()->hasRole('branch_factor_respons') || auth()->user()->hasRole('store_factor_response') || auth()->user()->hasRole('factor_response')) {
//             $operationOrders = OperationOrder::where('out_operation', 1)->latest()->get();
//             $operationOrdrDetails = OperationOrderDetail::whereHas('operationOrder', function ($q) {
//                 $q->where('out_operation', 1)->where('date','>=','2023-12-01');
//             })->whereDoesntHave('operationOrderResults')->latest()->get();
//         } else {
//             $operationOrders = OperationOrder::toUser()->where('out_operation', 1)->latest()->get();
//             $operationOrdersIds = $operationOrders->pluck('id')->toArray();
//             $operationOrdrDetails = OperationOrderDetail::whereIn('operation_order_id', $operationOrdersIds)->whereDoesntHave('operationOrderResults')->latest()->get();
//         }
//         // dd($operationOrders);
//         $damages = Damage::where('is_damage', 1)->where('group_id', 63)->latest()->get();
//         $scrap = Damage::where('is_damage', 1)->where('group_id', 71)->latest()->get();
//         $employees = Employee::where('branch_id',auth()->user()->branch_id)->where('job_id',2)->where('active', 1)->latest()->get();

//         return view('dashboard.operation_order_results.create_out', compact('operationOrders', 'operationOrdrDetails','scrap', 'damages','employees'));
//     }
    
    
//     /**
//      * Momaher
//      */
     
// //osama


// public function store(Request $request)
// {

//     // return $request->all();
//     $request->validate([
//         'order_details_id' => 'required',
//         'actual_output' => 'required',
//         'old_item_quantity' => 'required',
//         'notes',
//     ]);

//     $request_data = $request->except(['damage_type', 'old_damage_id', 'damage_quantity', 'damage_length', 'damage_width', 'damage_thickness', 'damage_price', 'damage_weight']);

//     $request_data['operation_order_id'] = OperationOrderDetail::where('id', $request->order_details_id)->first()->operation_order_id;
//     $request_data['order_details_id'] = $request->order_details_id;
//     if (isset($request['employee_id']) && !empty($request['employee_id'])) {
//         $request_data['employee_id'] = implode(',', $request->employee_id);
//     }
//     DB::beginTransaction();
//     $operationOrderResult = OperationOrderResult::create($request_data);
//     $operationOrder = OperationOrder::where('id', $operationOrderResult->operation_order_id)->first();
//     $operationOrderDetail = OperationOrderDetail::where('id', $operationOrderResult->order_details_id)->first();
//     // if(isset($reqeust['supply_quantity']) && isset($reqeust['supply_length'])){
//     //     $operationOrderDetail->supplie_quantity_used = $request['supply_quantity'] * $request['supply_length'];
//     //     $operationOrderDetail->save();
//     // }

//     $machine = Machines::where('id', $operationOrder->machine_id)->first();

//     $store = DB::table('stores')
//         ->select('id', 'name')
//         ->where('id', $machine->store_id)
//         ->first();

//     $supplies_id = explode(',', $operationOrderDetail->operation_suplies_id);

//     /**
//      * Tracking Machine supplie
//      */
//     foreach ($supplies_id as $supplie_id) {
//         $trackingMachineSupplie = TrackingMachineSupplies::where('date', $operationOrder->date)
//             ->where('supplie_id', $supplie_id)
//             ->where('machine_id', $operationOrder->machine_id)
//             ->first();
//         $new_tracking = TrackingMachineSupplies::create([
//             'machine_id' => $operationOrder->machine_id,
//             'supplie_id' => $supplie_id,
//             'date' => $operationOrder->date,
//             'type' => 'operation_in',
//             'quantity' => -$request['supply_quantity'] * $request['supply_length'],
//             // 'quantity' => $operationOrderDetail->supplie_quantity_used,
//             'operation_order_id' => $operationOrder->id,
//             'operation_order_result_id' => $operationOrderResult->id
//         ]);
//         // return $trackingMachineSupplie;
//         if (!$trackingMachineSupplie) {
//             $last_tracking = TrackingMachineSupplies::where('supplie_id', $supplie_id)
//                 ->where('machine_id', $operationOrder->machine_id)->where('id', '!=', $new_tracking->id)->orderBy('date', 'DESC')->latest()->first();
//             if ($last_tracking) {
//                 $new_tracking->init_quantity = $last_tracking->last_quantity;
//                 $new_tracking->last_quantity = $new_tracking->init_quantity + $new_tracking->quantity;
//                 $new_tracking->save();
//             } else {
//                 $new_tracking->init_quantity = MachineSupplie::where('machine_id', $operationOrder->machine_id)->where('supplie_id', $supplie_id)->first()->used;
//                 $new_tracking->last_quantity = $new_tracking->init_quantity + $new_tracking->quantity;
//                 $new_tracking->save();
//             }
//         } else {
//             $new_tracking->init_quantity = $trackingMachineSupplie->init_quantity;
//             $new_tracking->last_quantity = $trackingMachineSupplie->last_quantity + $new_tracking->quantity;
//             $new_tracking->save();
//         }
//     }
//     /**
//      * Decrement Used from supplies
//      */

//     $machine_supplies = MachineSupplie::where('machine_id', $operationOrder->machine_id)->whereIn('supplie_id', $supplies_id)->get();
//     foreach ($machine_supplies as $item) {
//         $supply_used = Supplies::where('id', $item->supplie_id)->first()->used;
//         $item->decrement('used', $request['supply_quantity'] * $request['supply_length']);
//         $item->quantity = ceil($item->used / $supply_used);
//         $item->save();
//     }
    
//     $damageWeightSum = 0;
//     if ($request->damage_quantity) {
//         $counter = 0;
//         foreach ($request->damage_quantity as $item) {
//             $damageWeightSum += $request->damage_weight[$counter];
            
//             $orderResultDetail = OperationOrderResultDetail::create([
//                 'operation_order_id'        => $operationOrderResult->operation_order_id,
//                 'order_details_id'      => $operationOrderResult->order_details_id,
//                 'order_results_id'      => $operationOrderResult->id,
//                 'damage_type'       => $request->damage_type[$counter],
//                 'damage_name'       => $request->damage_name[$counter],
//                 'damage_quantity'       => $request->damage_quantity[$counter],
//                 'damage_length'     => $request->damage_length[$counter],
//                 'damage_width'      => $operationOrderDetail->width,
//                 'damage_thickness'      => $operationOrderResult->thickness,
//                 'damage_price'      => $request->damage_price[$counter],
//                 'damage_weight'     => $request->damage_weight[$counter],
//             ]);
//             if ($request->old_damage_id != null) {
//                 $orderResultDetail->old_damage_id = $request->old_damage_id[$counter];
//                 $orderResultDetail->save();
//             }
//             $counter++;
//         }
//     }

//     $itemQuantity = Quantity::where('ownerable_type', 'App\Models\Store')
//         ->where('ownerable_id', $store->id)
//         ->where('item_id', $operationOrderDetail->item_id)
//         ->first();

//     if($request->old_item_quantity == 0 || $request->old_item_quantity + $damageWeightSum > $itemQuantity->quantity) {
//         session()->flash('error', 'انتبه: لا يمكن ان تكون كمية الخامة المستخدمة اكبر من المتاحة للألة: ' . $itemQuantity->quantity);
//         return back();
//     }

//     DB::commit();

//     //send notifications to Responsable users of machine branch

//     $machineBranch = DB::table('branches')->where('name', $store->name)->first();
//     $rolesIds = DB::table('roles')->whereIn('name', ['factor_response', 'branch_factor_respons', 'safe_factor_response'])->pluck('id')->toArray();
//     $usersRespons = DB::table('users')->where('branch_id', $machineBranch->id)->whereIn('role_id', $rolesIds)->pluck('id')->toArray();

//     for ($index = 0; $index < count($usersRespons); $index++) {
//         $this->push_notification(['user_id' => $usersRespons[$index], 'url' => url('operation_orders')]);
//     }

//     session()->flash('success', __('site.added_successfully'));
//     return redirect()->route('dashboard.operation_order_results.index');
// }


//     // public function store(Request $request)
//     // {
//     //     // dd($request->all());
//     //     $request->validate([
//     //         'order_details_id' => 'required',
//     //         'actual_output' => 'required',
//     //         'old_item_quantity' => 'required',
//     //         'notes',
//     //     ]);

//     //     $request_data = $request->except(['damage_type', 'old_damage_id', 'damage_quantity', 'damage_length', 'damage_width', 'damage_thickness', 'damage_price', 'damage_weight']);

//     //     $request_data['operation_order_id'] = OperationOrderDetail::where('id', $request->order_details_id)->first()->operation_order_id;
//     //     $request_data['order_details_id'] = $request->order_details_id;
//     //     if (isset($request['employee_id']) && !empty($request['employee_id'])) {
//     //         $request_data['employee_id'] = implode(',', $request->employee_id);
//     //     }
//     //     DB::beginTransaction();
//     //     $operationOrderResult = OperationOrderResult::create($request_data);
//     //     $operationOrder = OperationOrder::where('id', $operationOrderResult->operation_order_id)->first();
//     //     $operationOrderDetail = OperationOrderDetail::where('id', $operationOrderResult->order_details_id)->first();
//     //     // if(isset($reqeust['supply_quantity']) && isset($reqeust['supply_length'])){
//     //     //     $operationOrderDetail->supplie_quantity_used = $request['supply_quantity'] * $request['supply_length'];
//     //     //     $operationOrderDetail->save();
//     //     // }

//     //     $machine = Machines::where('id', $operationOrder->machine_id)->first();

//     //     // dd(  $machine);//1//3
//     // //    dd($operationOrder->machine_id);//1
//     //     $store = DB::table('stores')
//     //         ->select('id', 'name')
//     //         ->where('id', $machine->store_id)
//     //         ->first();

//     //     $supplies_id = explode(',', $operationOrderDetail->operation_suplies_id);

//     //     // dd(  $supplies_id);//108

//     //     /**
//     //      * Tracking Machine supplie
//     //      */
//     //     foreach ($supplies_id as $supplie_id) {
//     //         $trackingMachineSupplie = TrackingMachineSupplies::where('date', $operationOrder->date)
//     //             ->where('supplie_id', $supplie_id)
//     //             ->where('machine_id', $operationOrder->machine_id)
//     //             ->first();
//     //     // dd(  $trackingMachineSupplie->init_quantity);//null
                
//     //         $new_tracking = TrackingMachineSupplies::create([
//     //             'machine_id' => $operationOrder->machine_id,
//     //             'supplie_id' => $supplie_id,
//     //             'date' => $operationOrder->date,
//     //             'type' => 'operation_in',
//     //             'quantity' => -$request['supply_quantity'] * $request['supply_length'],
//     //             // 'quantity' => $operationOrderDetail->supplie_quantity_used,
//     //             'operation_order_id' => $operationOrder->id,
//     //             'operation_order_result_id' => $operationOrderResult->id
//     //         ]);

//     //     // dd(  $new_tracking);

//     //         // dd($trackingMachineSupplie);
//     //         if (!$trackingMachineSupplie) {
               
//     //             $last_tracking = TrackingMachineSupplies::where('supplie_id', $supplie_id)
//     //                 ->where('machine_id', $operationOrder->machine_id)
//     //                 ->where('id', '!=', $new_tracking->id)
//     //                 ->orderBy('date', 'DESC')
//     //                 ->latest()
//     //                 ->first();
//     //             // dd($last_tracking);
//     //             if ($last_tracking) {
                 
//     //                 $new_tracking->init_quantity = $last_tracking->last_quantity;
//     //                 $new_tracking->last_quantity = $new_tracking->init_quantity + $new_tracking->quantity;
//     //                 $new_tracking->save();
//     //             } else {

//     //                 $new_tracking->init_quantity = MachineSupplie::where('machine_id', $operationOrder->machine_id)->where('id', $supplie_id)->first()->used;
//     //                 $new_tracking->last_quantity = $new_tracking->init_quantity + $new_tracking->quantity;

//     //                 // dd($new_tracking->last_quantity);
//     //                 $new_tracking->save();
                 
//     //                 // $machineSupplie = MachineSupplie::where('machine_id', $operationOrder->machine_id)
//     //                 //     ->where('id', $supplie_id)
//     //                 //     ->first();
                        
//     //                 // dd( $machineSupplie);
                  
//     //                 // if (!$machineSupplie) {
//     //                 //     MachineSupplie::create([
//     //                 //         'machine_id' => $operationOrder->machine_id,
//     //                 //         'supplie_id' => $supplie_id,
//     //                 //         'used' => 0,  
//     //                 //     ]);
        
                       
//     //                 //     $machineSupplie = MachineSupplie::where('machine_id', $operationOrder->machine_id)
//     //                 //         ->where('supplie_id', $supplie_id)
//     //                 //         ->first();
//     //                 // }
        
                    
//     //                 // $new_tracking->init_quantity = $machineSupplie->used;
//     //                 // $new_tracking->last_quantity = $new_tracking->init_quantity + $new_tracking->quantity;
//     //                 // $new_tracking->save();
//     //             }
//     //         } else {
                
//     //             $new_tracking->init_quantity = $trackingMachineSupplie->init_quantity;
//     //             // dd( $new_tracking->init_quantity);
//     //             $new_tracking->last_quantity = $trackingMachineSupplie->last_quantity + $new_tracking->quantity;
//     //             $new_tracking->save();
//     //         }
//     //     }
//     //     /**
//     //      * Decrement Used from supplies
//     //      */

//     //      $machine_supplies = MachineSupplie::where('machine_id', $operationOrder->machine_id)->whereIn('id', $supplies_id)->get();
//     //     //  dd($machine_supplies);
//     //      foreach ($machine_supplies as $item) {
//     //          $supply_used = Supplies::where('id', $item->supplie_id)->first()->used;
//     //         //  dd($supply_used);
//     //          $item->decrement('used', $request['supply_quantity'] * $request['supply_length']);
//     //          $item->quantity = ceil($item->used / $supply_used);
//     //          $item->save();
         
//     //     }
        
//     //     $damageWeightSum = 0;
//     //     if ($request->damage_quantity) {
//     //         $counter = 0;
//     //         foreach ($request->damage_quantity as $item) {
//     //             $damageWeightSum += $request->damage_weight[$counter];
                
//     //             $orderResultDetail = OperationOrderResultDetail::create([
//     //                 'operation_order_id'        => $operationOrderResult->operation_order_id,
//     //                 'order_details_id'      => $operationOrderResult->order_details_id,
//     //                 'order_results_id'      => $operationOrderResult->id,
//     //                 'damage_type'       => $request->damage_type[$counter],
//     //                 'damage_name'       => $request->damage_name[$counter],
//     //                 'damage_quantity'       => $request->damage_quantity[$counter],
//     //                 'damage_length'     => $request->damage_length[$counter],
//     //                 'damage_width'      => $operationOrderDetail->width,
//     //                 'damage_thickness'      => $operationOrderResult->thickness,
//     //                 'damage_price'      => $request->damage_price[$counter],
//     //                 'damage_weight'     => $request->damage_weight[$counter],
//     //             ]);
//     //             if ($request->old_damage_id != null) {
//     //                 $orderResultDetail->old_damage_id = $request->old_damage_id[$counter];
//     //                 $orderResultDetail->save();
//     //             }
//     //             $counter++;
//     //         }
//     //     }

//     //     $itemQuantity = Quantity::where('ownerable_type', 'App\Models\Store')
//     //         ->where('ownerable_id', $store->id)
//     //         ->where('item_id', $operationOrderDetail->item_id)
//     //         ->first();

//     //     if($request->old_item_quantity == 0 || $request->old_item_quantity + $damageWeightSum > $itemQuantity->quantity) {
//     //         session()->flash('error', 'انتبه: لا يمكن ان تكون كمية الخامة المستخدمة اكبر من المتاحة للألة: ' . $itemQuantity->quantity);
//     //         return back();
//     //     }

//     //     DB::commit();

//     //     //send notifications to Responsable users of machine branch

//     //     $machineBranch = DB::table('branches')->where('name', $store->name)->first();
//     //     $rolesIds = DB::table('roles')->whereIn('name', ['factor_response', 'branch_factor_respons', 'safe_factor_response'])->pluck('id')->toArray();
//     //     $usersRespons = DB::table('users')->where('branch_id', $machineBranch->id)->whereIn('role_id', $rolesIds)->pluck('id')->toArray();

//     //     for ($index = 0; $index < count($usersRespons); $index++) {
//     //         $this->push_notification(['user_id' => $usersRespons[$index], 'url' => url('operation_orders')]);
//     //     }

//     //     session()->flash('success', __('site.added_successfully'));
//     //     return redirect()->route('dashboard.operation_order_results.index');
//     // }

//     public function storeOut(Request $request)
//     {
//         // return $request;
//         $request->validate([
//             'order_details_id' => 'required',
//             'actual_output' => 'required',
//             'old_item_quantity' => 'required',
//             'total_used_length' => 'required',
//             'notes',
//         ]);

//         DB::beginTransaction();

//         $request_data = $request->except(['damage_type', 'old_damage_id', 'damage_quantity', 'damage_length', 'damage_width', 'damage_thickness', 'damage_price', 'damage_weight']);
//         // dd($request_data);
//         $request_data['operation_order_id'] = OperationOrderDetail::where('id', $request->order_details_id)->first()->operation_order_id;
//         $request_data['order_details_id'] = $request->order_details_id;
//                 if (isset($request['employee_id']) && !empty($request['employee_id'])) {
//             $request_data['employee_id'] = implode(',', $request->employee_id);
//         }
//         $operationOrderResult = OperationOrderResult::create($request_data);
//         $operationOrder = OperationOrder::where('id', $operationOrderResult->operation_order_id)->first();
//         $operationOrderDetail = OperationOrderDetail::where('id', $operationOrderResult->order_details_id)->first();
        
//         $operationOrder->total_used_length = $request->total_used_length;
//         $operationOrder->save();
//         // if(isset($reqeust['supply_quantity']) && isset($reqeust['supply_length'])){
//         //     $operationOrderDetail->supplie_quantity_used = $request['supply_quantity'] * $request['supply_length'];
//         //     $operationOrderDetail->save();
//         // }
//         // $operationOrderDetail->supplie_quantity_used = $operationOrderResult->operationOrder->total_used_length;
//         // $operationOrderDetail->save();
//         $machine = Machines::where('id', $operationOrder->machine_id)->first();
//         $store = DB::table('stores')
//             ->select('id', 'name')
//             ->where('id', $machine->store_id)
//             ->first();

//         $supplies_id = explode(',', $operationOrderDetail->operation_suplies_id);

//         /**
//          * Tracking Machine supplie
//          */
//         foreach ($supplies_id as $supplie_id) {
//             $trackingMachineSupplie = TrackingMachineSupplies::where('date', $operationOrder->date)
//                 ->orderBy('date', 'DESC')
//                 ->where('supplie_id', $supplie_id)
//                 ->where('machine_id', $operationOrder->machine_id)
//                 ->latest()->first();
//             $new_tracking = TrackingMachineSupplies::create([
//                 'machine_id' => $operationOrder->machine_id,
//                 'supplie_id' => $supplie_id,
//                 'date' => $operationOrder->date,
//                 'type' => 'operation_out',
//                 'quantity' => -$request['supply_quantity'] * $request['supply_length'],
//                 // 'quantity' => $operationOrderDetail->supplie_quantity_used,
//                 'operation_order_id' => $operationOrder->id,
//                 'operation_order_result_id' => $operationOrderResult->id
//             ]);
//             // return $trackingMachineSupplie;
//             if (!$trackingMachineSupplie) {
//                 $last_tracking = TrackingMachineSupplies::where('supplie_id', $supplie_id)
//                     ->where('machine_id', $operationOrder->machine_id)->orderBy('date', 'DESC')->where('id', '!=', $new_tracking->id)->latest()->first();
//                 if ($last_tracking) {
//                     $new_tracking->init_quantity = $last_tracking->last_quantity;
//                     $new_tracking->last_quantity = $new_tracking->init_quantity + $new_tracking->quantity;
//                     $new_tracking->save();
//                 } else {

//                     $new_tracking->init_quantity = MachineSupplie::where('machine_id', $operationOrder->machine_id)->where('supplie_id', $supplie_id)->first()->used;
//                     $new_tracking->last_quantity = $new_tracking->init_quantity + $new_tracking->quantity;
//                     $new_tracking->save();
//                 }
//             } else {
//                 $new_tracking->init_quantity = $trackingMachineSupplie->init_quantity;
//                 $new_tracking->last_quantity = $trackingMachineSupplie->last_quantity + $new_tracking->quantity;
//                 $new_tracking->save();
//             }
//         }
//         /**
//          * Decrement Used from supplies
//          */
//         $machine_supplies = MachineSupplie::where('machine_id', $operationOrder->machine_id)->whereIn('supplie_id', $supplies_id)->get();
//         foreach ($machine_supplies as $item) {
//             $item->decrement('used', $request['supply_quantity'] * $request['supply_length']);
//         }
        
//         if ($request->damage_quantity) {
//             $counter = 0;
//             foreach ($request->damage_quantity as $item) {
//                 $orderResultDetail = OperationOrderResultDetail::create([
//                     'operation_order_id'        => $operationOrderResult->operation_order_id,
//                     'order_details_id'      => $operationOrderResult->order_details_id,
//                     'order_results_id'      => $operationOrderResult->id,
//                     'damage_type'       => $request->damage_type[$counter],
//                     'damage_name'       => $request->damage_name[$counter],
//                     'damage_quantity'       => $request->damage_quantity[$counter],
//                     'damage_length'     => $request->damage_length[$counter],
//                     'damage_width'      => $operationOrderDetail->width,
//                     'damage_thickness'      => $operationOrderResult->thickness,
//                     'damage_price'      => $request->damage_price[$counter],
//                     'damage_weight'     => $request->damage_weight[$counter],
//                 ]);
//                 if ($request->old_damage_id != null) {
//                     $orderResultDetail->old_damage_id = $request->old_damage_id[$counter];
//                     $orderResultDetail->save();
//                 }
//                 $counter++;
//             }
//             /**
//              * Tracking Machine supplie
//              */
//             $trackingMachineSupplie = TrackingMachineSupplies::where('date', $operationOrderResult->date)->first();
//             $new_tracking = TrackingMachineSupplies::create([
//                 'machine_id' => $operationOrderResult->operationOrder->machine_id,
//                 'supplie_id' => $operationOrderResult->operationOrderDetail->operation_suplies_id,
//                 'date' => $operationOrderResult->operationOrder->date,
//                 'type' => 'operation_out',
//                 'quantity' => $operationOrderResult->operationOrderDetail->supplie_quantity_used,
//                 'operation_order_id' => $orderResultDetail->id
//             ]);
//             if (!$trackingMachineSupplie) {
//                 $new_tracking->init_quantity = TrackingMachineSupplies::latest()->first()->init_quantity;
//                 $new_tracking->save();
//             } else {
//                 $new_tracking->init_quantity = $trackingMachineSupplie->init_quantity;
//                 $new_tracking->save();
//             }
//         }
        
//         DB::commit();
//         //send notifications to Responsable users of machine branch

//         $machineBranch = DB::table('branches')->where('name', $store->name)->first();
//         $rolesIds = DB::table('roles')->whereIn('name', ['factor_response', 'branch_factor_respons', 'safe_factor_response'])->pluck('id')->toArray();
//         $usersRespons = DB::table('users')->where('branch_id', $machineBranch->id)->whereIn('role_id', $rolesIds)->pluck('id')->toArray();

//         for ($index = 0; $index < count($usersRespons); $index++) {
//             $this->push_notification(['user_id' => $usersRespons[$index], 'url' => url('operation_orders')]);
//         }

//         session()->flash('success', __('site.added_successfully'));
//         return redirect()->route('dashboard.operation_order_results.index_out');
//     }
     
//     /**
//      * Momaher
//      */
     

//     // public function store(Request $request)
//     // {

//     //     // return $request->all();
//     //     $request->validate([
//     //         'order_details_id' => 'required',
//     //         'actual_output' => 'required',
//     //         'old_item_quantity' => 'required',
//     //         'notes',
//     //     ]);

//     //     $request_data = $request->except(['damage_type', 'old_damage_id', 'damage_quantity', 'damage_length', 'damage_width', 'damage_thickness', 'damage_price', 'damage_weight']);

//     //     $request_data['operation_order_id'] = OperationOrderDetail::where('id', $request->order_details_id)->first()->operation_order_id;
//     //     $request_data['order_details_id'] = $request->order_details_id;

//     //     DB::beginTransaction();
//     //     $operationOrderResult = OperationOrderResult::create($request_data);
//     //     $operationOrder = OperationOrder::where('id', $operationOrderResult->operation_order_id)->first();
//     //     $operationOrderDetail = OperationOrderDetail::where('id', $operationOrderResult->order_details_id)->first();


//     //     $machine = Machines::where('id', $operationOrder->machine_id)->first();
//     //     $store = DB::table('stores')
//     //         ->select('id', 'name')
//     //         ->where('id', $machine->store_id)
//     //         ->first();

//     //     /**
//     //      * Decrement Used from supplies
//     //      */
//     //     $supplies_id = explode(',', $operationOrderDetail->operation_suplies_id);

//     //     $machine_supplies = MachineSupplie::where('machine_id', $operationOrder->machine_id)->whereIn('supplie_id', $supplies_id)->get();
//     //     foreach ($machine_supplies as $item) {
//     //         $supply_used = Supplies::where('id', $item->supplie_id)->first()->used;
//     //         $item->decrement('used', $request['supply_quantity'] * $request['supply_length']);
//     //         $item->quantity = ceil($item->used / $supply_used);
//     //         $item->save();
//     //     }
//     //     if ($request->damage_quantity) {
//     //         $counter = 0;
//     //         foreach ($request->damage_quantity as $item) {
//     //             $orderResultDetail = OperationOrderResultDetail::create([
//     //                 'operation_order_id'        => $operationOrderResult->operation_order_id,
//     //                 'order_details_id'      => $operationOrderResult->order_details_id,
//     //                 'order_results_id'      => $operationOrderResult->id,
//     //                 'damage_type'       => $request->damage_type[$counter],
//     //                 'damage_name'       => $request->damage_name[$counter],
//     //                 'damage_quantity'       => $request->damage_quantity[$counter],
//     //                 'damage_length'     => $request->damage_length[$counter],
//     //                 'damage_width'      => $operationOrderDetail->width,
//     //                 'damage_thickness'      => $operationOrderResult->thickness,
//     //                 'damage_price'      => $request->damage_price[$counter],
//     //                 'damage_weight'     => $request->damage_weight[$counter],
//     //             ]);
//     //             if ($request->old_damage_id != null) {
//     //                 $orderResultDetail->old_damage_id = $request->old_damage_id[$counter];
//     //                 $orderResultDetail->save();
//     //             }
//     //             $counter++;
//     //         }
//     //     }
//     //     DB::commit();

//     //     //send notifications to Responsable users of machine branch

//     //     $machineBranch = DB::table('branches')->where('name', $store->name)->first();
//     //     $rolesIds = DB::table('roles')->whereIn('name', ['factor_response', 'branch_factor_respons'])->pluck('id')->toArray();
//     //     $usersRespons = DB::table('users')->where('branch_id', $machineBranch->id)->whereIn('role_id', $rolesIds)->pluck('id')->toArray();

//     //     for ($index = 0; $index < count($usersRespons); $index++) {
//     //         $this->push_notification(['user_id' => $usersRespons[$index], 'url' => url('operation_orders')]);
//     //     }

//     //     session()->flash('success', __('site.added_successfully'));
//     //     return redirect()->route('dashboard.operation_order_results.index');
//     // }

//     // public function storeOut(Request $request)
//     // {

//     //     $request->validate([
//     //         'order_details_id' => 'required',
//     //         'actual_output' => 'required',
//     //         'old_item_quantity' => 'required',
//     //         'total_used_length' => 'required',
//     //         'notes',
//     //     ]);

//     //     $request_data = $request->except(['damage_type', 'total_used_length', 'old_damage_id', 'damage_quantity', 'damage_length', 'damage_width', 'damage_thickness', 'damage_price', 'damage_weight']);
//     //     // dd($request_data);
//     //     $request_data['operation_order_id'] = OperationOrderDetail::where('id', $request->order_details_id)->first()->operation_order_id;
//     //     $request_data['order_details_id'] = $request->order_details_id;
//     //     $operationOrderResult = OperationOrderResult::create($request_data);
//     //     $operationOrder = OperationOrder::where('id', $operationOrderResult->operation_order_id)->first();
//     //     $operationOrder->total_used_length = $request->total_used_length;
//     //     $operationOrder->save();
//     //     $operationOrderDetail = OperationOrderDetail::where('id', $operationOrderResult->order_details_id)->first();

//     //     $machine = Machines::where('id', $operationOrder->machine_id)->first();
//     //     $store = DB::table('stores')
//     //         ->select('id', 'name')
//     //         ->where('id', $machine->store_id)
//     //         ->first();

//     //     /**
//     //      * Decrement Used from supplies
//     //      */
//     //     $supplies_id = explode(',', $operationOrderDetail->operation_suplies_id);
//     //     $machine_supplies = MachineSupplie::where('machine_id', $operationOrder->machine_id)->whereIn('supplie_id', $supplies_id)->get();
//     //     foreach ($machine_supplies as $item) {
//     //         $item->decrement('used', $request['supply_quantity'] * $request['supply_length']);
//     //     }
//     //     if ($request->damage_quantity) {
//     //         $counter = 0;
//     //         foreach ($request->damage_quantity as $item) {
//     //             $orderResultDetail = OperationOrderResultDetail::create([
//     //                 'operation_order_id'        => $operationOrderResult->operation_order_id,
//     //                 'order_details_id'      => $operationOrderResult->order_details_id,
//     //                 'order_results_id'      => $operationOrderResult->id,
//     //                 'damage_type'       => $request->damage_type[$counter],
//     //                 'damage_name'       => $request->damage_name[$counter],
//     //                 'damage_quantity'       => $request->damage_quantity[$counter],
//     //                 'damage_length'     => $request->damage_length[$counter],
//     //                 'damage_width'      => $operationOrderDetail->width,
//     //                 'damage_thickness'      => $operationOrderResult->thickness,
//     //                 'damage_price'      => $request->damage_price[$counter],
//     //                 'damage_weight'     => $request->damage_weight[$counter],
//     //             ]);
//     //             if ($request->old_damage_id != null) {
//     //                 $orderResultDetail->old_damage_id = $request->old_damage_id[$counter];
//     //                 $orderResultDetail->save();
//     //             }
//     //             $counter++;
//     //         }
//     //     }

//     //     //send notifications to Responsable users of machine branch

//     //     $machineBranch = DB::table('branches')->where('name', $store->name)->first();
//     //     $rolesIds = DB::table('roles')->whereIn('name', ['factor_response', 'branch_factor_respons'])->pluck('id')->toArray();
//     //     $usersRespons = DB::table('users')->where('branch_id', $machineBranch->id)->whereIn('role_id', $rolesIds)->pluck('id')->toArray();

//     //     for ($index = 0; $index < count($usersRespons); $index++) {
//     //         $this->push_notification(['user_id' => $usersRespons[$index], 'url' => url('operation_orders')]);
//     //     }

//     //     session()->flash('success', __('site.added_successfully'));
//     //     return redirect()->route('dashboard.operation_order_results.index_out');
//     // }

//     // public function store(Request $request)
//     // {

//     //     $request->validate([
//     //         'order_details_id' => 'required',
//     //         'actual_output' => 'required',
//     //         'old_item_quantity' => 'required',
//     //         'notes',
//     //     ]);

//     //     $request_data = $request->except(['damage_type', 'old_damage_id', 'damage_quantity', 'damage_length', 'damage_width', 'damage_thickness', 'damage_price', 'damage_weight']);

//     //     $request_data['operation_order_id'] = OperationOrderDetail::where('id', $request->order_details_id)->first()->operation_order_id;
//     //     $request_data['order_details_id'] = $request->order_details_id;
//     //     $operationOrderResult = OperationOrderResult::create($request_data);
//     //     $operationOrder = OperationOrder::where('id', $operationOrderResult->operation_order_id)->first();
//     //     $operationOrderDetail = OperationOrderDetail::where('id', $operationOrderResult->order_details_id)->first();

//     //     $machine = Machines::where('id', $operationOrder->machine_id)->first();
//     //     $store = DB::table('stores')
//     //         ->select('id', 'name')
//     //         ->where('id', $machine->store_id)
//     //         ->first();

//     //     if ($request->damage_quantity) {
//     //         $counter = 0;
//     //         foreach ($request->damage_quantity as $item) {
//     //             $orderResultDetail = OperationOrderResultDetail::create([
//     //                 'operation_order_id'        => $operationOrderResult->operation_order_id,
//     //                 'order_details_id'      => $operationOrderResult->order_details_id,
//     //                 'order_results_id'      => $operationOrderResult->id,
//     //                 'damage_type'       => $request->damage_type[$counter],
//     //                 'damage_name'       => $request->damage_name[$counter],
//     //                 'damage_quantity'       => $request->damage_quantity[$counter],
//     //                 'damage_length'     => $request->damage_length[$counter],
//     //                 'damage_width'      => $operationOrderDetail->width,
//     //                 'damage_thickness'      => $operationOrderResult->thickness,
//     //                 'damage_price'      => $request->damage_price[$counter],
//     //                 'damage_weight'     => $request->damage_weight[$counter],
//     //             ]);
//     //             if ($request->old_damage_id != null) {
//     //                 $orderResultDetail->old_damage_id = $request->old_damage_id[$counter];
//     //                 $orderResultDetail->save();
//     //             }
//     //             $counter++;
//     //         }
//     //     }

//     //     //send notifications to Responsable users of machine branch

//     //     $machineBranch = DB::table('branches')->where('name', $store->name)->first();
//     //     $rolesIds = DB::table('roles')->whereIn('name', ['factor_response', 'branch_factor_respons'])->pluck('id')->toArray();
//     //     $usersRespons = DB::table('users')->where('branch_id', $machineBranch->id)->whereIn('role_id', $rolesIds)->pluck('id')->toArray();

//     //     for ($index = 0; $index < count($usersRespons); $index++) {
//     //         $this->push_notification(['user_id' => $usersRespons[$index], 'url' => url('operation_orders')]);
//     //     }

//     //     session()->flash('success', __('site.added_successfully'));
//     //     return redirect()->route('dashboard.operation_order_results.index');use App\Employee;
//     // }

//     // public function storeOut(Request $request)
//     // {

//     //     $request->validate([
//     //         'order_details_id' => 'required',
//     //         'actual_output' => 'required',
//     //         'old_item_quantity' => 'required',
//     //         'total_used_length' => 'required',
//     //         'notes',
//     //     ]);

//     //     $request_data = $request->except(['damage_type', 'total_used_length', 'old_damage_id', 'damage_quantity', 'damage_length', 'damage_width', 'damage_thickness', 'damage_price', 'damage_weight']);
//     //     // dd($request_data);
//     //     $request_data['operation_order_id'] = OperationOrderDetail::where('id', $request->order_details_id)->first()->operation_order_id;
//     //     $request_data['order_details_id'] = $request->order_details_id;
//     //     $operationOrderResult = OperationOrderResult::create($request_data);
//     //     $operationOrder = OperationOrder::where('id', $operationOrderResult->operation_order_id)->first();
//     //     $operationOrder->total_used_length = $request->total_used_length;
//     //     $operationOrder->save();

//     //     $machine = Machines::where('id', $operationOrder->machine_id)->first();
//     //     $store = DB::table('stores')
//     //         ->select('id', 'name')
//     //         ->where('id', $machine->store_id)
//     //         ->first();


//     //     //send notifications to Responsable users of machine branch

//     //     $machineBranch = DB::table('branches')->where('name', $store->name)->first();
//     //     $rolesIds = DB::table('roles')->whereIn('name', ['factor_response', 'branch_factor_respons'])->pluck('id')->toArray();
//     //     $usersRespons = DB::table('users')->where('branch_id', $machineBranch->id)->whereIn('role_id', $rolesIds)->pluck('id')->toArray();

//     //     for ($index = 0; $index < count($usersRespons); $index++) {
//     //         $this->push_notification(['user_id' => $usersRespons[$index], 'url' => url('operation_orders')]);
//     //     }

//     //     session()->flash('success', __('site.added_successfully'));
//     //     return redirect()->route('dashboard.operation_order_results.index_out');
//     // }

//     public function edit($operationOrderResult)
//     {
//         $operationOrders = OperationOrder::latest()->get();
//         $operationOrderResult = OperationOrderResult::find($operationOrderResult);
//         $operationOrdersIds = $operationOrders->pluck('id')->toArray();
//         $operationOrdrDetails = OperationOrderDetail::whereIn('operation_order_id', $operationOrdersIds)->latest()->get();


//         return view('dashboard.operation_order_results.edit', compact('operationOrderResult', 'operationOrdrDetails', 'operationOrders'));
//     }

//     public function update(Request $request, $operationOrderResult)
//     {
//         $request->validate([
//             'order_details_id' => 'required',
//             'actual_output' => 'required',
//             'notes',
//         ]);
//         $request_data = $request->except(['damage_type', 'old_damage_id', 'damage_quantity', 'damage_length', 'damage_width', 'damage_thickness', 'damage_price', 'damage_weight']);

//         $operationOrderResult = OperationOrderResult::find($operationOrderResult);
//         $operationOrder = OperationOrder::where('id', $operationOrderResult->operation_order_id)->first();
//         $operationOrderDetail = OperationOrderDetail::where('id', $operationOrderResult->order_details_id)->first();

//         $machine = Machines::where('id', $operationOrder->machine_id)->first();
//         $store = DB::table('stores')
//             ->select('id', 'name')
//             ->where('id', $machine->store_id)
//             ->first();

//         if ($operationOrderDetail->out_item_id) {
//             $quantity = Quantity::where('ownerable_type', 'App\Models\Store')
//                 ->where('ownerable_id', $store->id)
//                 ->where('item_id', $operationOrderDetail->out_item_id)
//                 ->first();
//             if ($operationOrderResult->confirmed) {
//                 $quantity->decrement('quantity', $operationOrderResult->actual_output);
//                 $quantity->save();
//             }
//         }
//         if ($operationOrderDetail->item_id) {
//             $quantity = Quantity::where('ownerable_type', 'App\Models\Store')
//                 ->where('ownerable_id', $store->id)
//                 ->where('item_id', $operationOrderDetail->item_id)
//                 ->first();
//             if ($operationOrderResult->confirmed) {
//                 $quantity->increment('quantity', $operationOrderResult->old_item_quantity);
//                 $quantity->save();
//             }
//         }
//         $request_data['confirmed'] = 0;
//         $operationOrderResult->update($request_data);

//         session()->flash('success', __('site.updated_successfully'));
//         return redirect()->route('dashboard.operation_order_results.index');
//     }


//     public function destroy($operationOrderResult)
//     {
//         $operationOrderResult = OperationOrderResult::find($operationOrderResult);
        
//         if(!empty($operationOrderResult)) {
//             $operationOrder = OperationOrder::where('id', $operationOrderResult->operation_order_id)->first();
//             $operationOrderDetail = OperationOrderDetail::where('id', $operationOrderResult->order_details_id)->first();
    
//             $machine = Machines::where('id', $operationOrder->machine_id)->first();
//             $store = DB::table('stores')
//                 ->select('id', 'name')
//                 ->where('id', $machine->store_id)
//                 ->first();
                
//             $supplies_id = explode(',', $operationOrderDetail->operation_suplies_id);
    
//             $machine_supplies = MachineSupplie::where('machine_id', $operationOrder->machine_id)->whereIn('supplie_id', $supplies_id)->get();
//             foreach ($machine_supplies as $item) {
//                 $item->increment('used', $operationOrderResult->total_used_length);
//                 // $item->increment('used', $operationOrderDetail->supplie_quantity_used);
//             }
    
//             if ($operationOrderDetail->out_item_id) {
//                 $quantity = Quantity::where('ownerable_type', 'App\Models\Store')
//                     ->where('ownerable_id', $store->id)
//                     ->where('item_id', $operationOrderDetail->out_item_id)
//                     ->first();
//                 if ($operationOrderResult->store_confirm) {
//                     $quantity->decrement('quantity', $operationOrderResult->actual_output);
//                     $quantity->save();
//                 }
//             }
//             if ($operationOrderDetail->item_id) {
//                 $quantity = Quantity::where('ownerable_type', 'App\Models\Store')
//                     ->where('ownerable_id', $store->id)
//                     ->where('item_id', $operationOrderDetail->item_id)
//                     ->first();
//                 if ($operationOrderResult->store_confirm) {
//                     $quantity->increment('quantity', $operationOrderResult->old_item_quantity);
//                     $quantity->save();
//                 }
//             }
    
//             if ($operationOrderDetail->is_special) {
//                 $item = DB::table('items')
//                     ->select('id', 'name')
//                     ->where('operat_ord_id', $operationOrder->id)
//                     ->where('is_special', 1)
//                     ->first();
    
//                 if ($item) {
//                     $quantity = Quantity::where('ownerable_type', 'App\Models\Store')
//                         ->where('ownerable_id', $store->id)
//                         ->where('item_id', $item->id)
//                         ->first();
    
//                     if ($operationOrderResult->store_confirm) {
//                         $quantity->decrement('quantity', $operationOrderResult->actual_output);
//                         $quantity->save();
//                     }
//                 }
//             }
    
//             if (!@empty($operationOrderResult->orderResultDetails)) {
//                 foreach ($operationOrderResult->orderResultDetails as $orderResultDetail) {
    
//                     $item = DB::table('items')
//                         ->select('id', 'name')
//                         ->where('operat_ord_id', $operationOrder->id)
//                         ->where('is_damage', 1)
//                         ->where('name', $orderResultDetail->damage_name)
//                         ->first();
//                     if ($item) {
//                         $quantity = Quantity::where('ownerable_type', 'App\Models\Store')
//                             ->where('ownerable_id', $store->id)
//                             ->where('item_id', $item->id)
//                             ->first();
//                         $quantity->decrement('quantity', $orderResultDetail->damage_quantity);
//                         $quantity->save();
    
//                         $orderResultDetail->delete();
//                     }
//                 }
//             }
//             TrackingMachineSupplies::where('operation_order_result_id', $operationOrderResult->id)->delete();
//             // $operationOrderResult->update(['is_deleted' => 1]);
//             $operationOrder->update(['store_edit' => 1]);
//             $operationOrderResult->delete();
//         }
        
//         session()->flash('success', __('site.deleted_successfully'));
//         return back();
//     }
//     //

//     public function order_result_delete_detail($id)
//     {
//         $orderResultDetail = OperationOrderResultDetail::where('id', $id)->first();
//         $operationOrderResult = OperationOrderResult::where('id', $orderResultDetail->order_results_id)->first();
//         $operationOrder = OperationOrder::where('id', $orderResultDetail->operation_order_id)->first();
//         $machine = Machines::where('id', $operationOrder->machine_id)->first();
//         $store = DB::table('stores')
//             ->select('id', 'name')
//             ->where('id', $machine->store_id)
//             ->first();

//         $item = DB::table('items')
//             ->select('id', 'name')
//             ->where('operat_ord_id', $operationOrder->id)
//             ->where('is_damage', 1)
//             ->where('name', $orderResultDetail->damage_name)
//             ->first();

//         $quantity = Quantity::where('ownerable_type', 'App\Models\Store')
//             ->where('ownerable_id', $store->id)
//             ->where('item_id', $item->id)
//             ->first();
//         $quantity->decrement('quantity', $orderResultDetail->damage_quantity);
//         $quantity->save();



//         $orderResultDetail->delete();
//         session()->flash('success', __('site.deleted_successfully'));
//         return back();
//     }
    
//     public function getOpertOrderInfo(Request $request)
//     {
//         if (!$request->operation_order_detail_id) {
//             $html = '';
//             $html .= '<h5>برجاء اختيار امر التشغيل</h5>';
//         } else {
//             $html = '';
//             $operationOrderDetails = OperationOrderDetail::where('id', $request->operation_order_detail_id)->get();
//             foreach ($operationOrderDetails as $operationOrderDetail) {
//                 $machine_supplies_id = explode(',', $operationOrderDetail->operation_suplies_id);
//                 // $machineSupplies = MachineSupplie::where("machine_id", $request->machine_id)
//                 // ->where("used", ">", 1)
//                 // ->get();
//                 $operationOrders = OperationOrder::where('id', $operationOrderDetail->operation_order_id)->get();
//                 foreach ($operationOrders as $operationOrder) {
//                     $used = MachineSupplie::where('machine_id', $operationOrder->machine_id)->whereIn('supplie_id', $machine_supplies_id)->pluck('used')->toArray();
//                     $empIds = explode(',', $operationOrder->employee_id);
//                     $notes = $operationOrder->notes;
//                     $employeesNames = Employee::whereIn('id', $empIds)->pluck('name')->toArray();

//                     $supplie_name = MachineSupplie::with('supplie')
//                         ->where('machine_id', $operationOrder->machine_id)
//                         ->whereIn('supplie_id', $machine_supplies_id)
//                         ->get();

//                     $supplieNames = $supplie_name->pluck('supplie.name')->toArray();

//                     // dd($supplieNames);


//                     $html .= '<h5 style="text-align: center;">بيانات أمر الشغل رقم  : ' . $operationOrder->id . '</h5>';
//                     $html .= '<h5 style="display:inline-block;max-width:300px;min-width:100px;margin-left: 65px;">الآلة : ' . optional($operationOrder->machine)->name . '</h5>';
//                     $html .= '<h5 style="display:inline-block;max-width:300px;min-width:100px;margin-left: 65px;">المستخدم : ' . optional($operationOrder->user)->name . '</h5>';
//                     // $html .= '<h5 style="display:inline-block;max-width:300px;min-width:100px;margin-left: 65px;">مشرف : ' . optional($operationOrder->supervisor)->name . '</h5>';
//                     $html .= '<h5 style="display:inline-block;max-width:350px;min-width:100px;margin-left: 65px;">الموظفين : ' . implode(" , ", $employeesNames) . '</h5>';
//                     $html .= '<h5 style="display:inline-block;max-width:300px;min-width:100px;margin-left: 65px;">التاريخ : ' . $operationOrder->date . '</h5>';
//                     $html .= '<br>';

//                     if (!empty($supplieNames)) {
//                         $html .= '<h5 style="display:inline-block;max-width:350px;min-width:100px;margin-left: 65px;"> المستلزم : ' . implode(" , ", $supplieNames) . '</h5>';
//                     }
//                     if ($operationOrder->out_operation == 0) {
//                         $html .= '<h5 style="display:inline-block;max-width:350px;min-width:100px;margin-left: 65px;">اسم الخامة : ' . optional($operationOrderDetail->item)->name . '</h5>';
//                         // $html .= '<h5 style="display:inline-block;max-width:300px;min-width:100px;margin-left: 65px;">اسم الناتج : ' . $operationOrderDetail->out_name . '</h5>';
//                         $itemName = DB::table('items')->where('id', $operationOrderDetail->out_item_id)->first();
//                         $html .= '<h5 style="display:inline-block;max-width:300px;min-width:100px;margin-left: 65px;">اسم الناتج : ' . optional($itemName)->name . '</h5>';
//                     } else {
//                         $html .= '<h5 style="display:inline-block;max-width:350px;min-width:100px;margin-left: 65px;">اسم الخامة المستخدمة : ' . optional($operationOrderDetail)->item_name . '</h5>';
//                         $html .= '<h5 style="display:inline-block;max-width:300px;min-width:100px;margin-left: 65px;">كمية الخامة المستخدمة : ' . optional($operationOrderDetail)->old_item_quantity . '</h5>';
//                         $html .= '<h5 style="display:inline-block;max-width:300px;min-width:100px;margin-left: 65px;">الخامة الناتجة : ' . $operationOrderDetail->out_item_name . '</h5>';
//                     }
//                     // $html .= '<h5 style="display:inline-block;max-width:300px;min-width:100px;margin-left: 65px;">الطول : ' .$machineSupplies->Supplie['id'].'</h5>';
//                     $html .= '<h5 style="display:inline-block;max-width:300px;min-width:100px;margin-left: 65px;">الطول : ' . $operationOrderDetail->length . '</h5>';
//                     $html .= '<h5 style="display:inline-block;max-width:300px;min-width:100px;margin-left: 65px;">العرض : ' . $operationOrderDetail->width . '</h5>';
//                     // $html .= '<h5 style="display:inline-block;max-width:300px;min-width:100px;margin-left: 65px;">السعر : '.$operationOrderDetail->price .'</h5>';
//                     $html .= '<h5 style="display:inline-block;max-width:300px;min-width:100px;margin-left: 65px;">الكمية : ' . $operationOrderDetail->quantity . '</h5>';
//                     // $html .= '<h5 style="display:inline-block;max-width:300px;min-width:100px;margin-left: 65px;">ملاحظات : ' . $notes . '</h5>';
//                     $html .= '<h5 style="display:inline-block;max-width:100%;min-width:100%;margin-left: 65px;word-wrap: break-word;">ملاحظات : ' . $notes . '</h5>';
//                     // if (auth()->user()->can('update_operation_orders')) {
//                     //     $html .= '<a class="btn btn-warning" style="text-decoration: none;" target="_blank" href="' . route("dashboard.operation_orders.edit", $operationOrder->id) . '" ><i class="fa fa-edit"></i> Edit</a>';
//                     // }
//                 }
//             }
//         }

//         return response()->json(['html' => $html, 'used' => $used]);
//     }
    
//     // public function getOpertOrderInfo(Request $request)
//     // {
//     //     if (!$request->operation_order_detail_id) {
//     //         $html = '';
//     //         $html .= '<h5>برجاء اختيار امر التشغيل</h5>';
//     //     } else {
//     //         $html = '';
//     //         $operationOrderDetails = OperationOrderDetail::where('id', $request->operation_order_detail_id)->get();
//     //         foreach ($operationOrderDetails as $operationOrderDetail) {
//     //             $operationOrders = OperationOrder::where('id', $operationOrderDetail->operation_order_id)->get();
//     //             foreach ($operationOrders as $operationOrder) {
//     //                 $empIds = explode(',', $operationOrder->employee_id);
//     //                 $employeesNames = Employee::whereIn('id', $empIds)->pluck('name')->toArray();

//     //                 $html .= '<h5 style="text-align: center;">بيانات أمر الشغل رقم  : ' . $operationOrder->id . '</h5>';
//     //                 $html .= '<h5 style="display:inline-block;max-width:300px;min-width:100px;margin-left: 65px;">الآلة : ' . optional($operationOrder->machine)->name . '</h5>';
//     //                 $html .= '<h5 style="display:inline-block;max-width:300px;min-width:100px;margin-left: 65px;">المستخدم : ' . optional($operationOrder->user)->name . '</h5>';
//     //                 // $html .= '<h5 style="display:inline-block;max-width:300px;min-width:100px;margin-left: 65px;">مشرف : ' . optional($operationOrder->supervisor)->name . '</h5>';
//     //                 $html .= '<h5 style="display:inline-block;max-width:350px;min-width:100px;margin-left: 65px;">الموظفين : ' . implode(" , ", $employeesNames) . '</h5>';
//     //                 $html .= '<h5 style="display:inline-block;max-width:300px;min-width:100px;margin-left: 65px;">التاريخ : ' . $operationOrder->date . '</h5>';
//     //                 $html .= '<br>';
//     //                 if ($operationOrder->out_operation == 0) {
//     //                     $html .= '<h5 style="display:inline-block;max-width:350px;min-width:100px;margin-left: 65px;">اسم الخامة : ' . optional($operationOrderDetail->item)->name . '</h5>';
//     //                     $html .= '<h5 style="display:inline-block;max-width:300px;min-width:100px;margin-left: 65px;">اسم الناتج : ' . $operationOrderDetail->out_name . '</h5>';
//     //                 } else {
//     //                     $html .= '<h5 style="display:inline-block;max-width:350px;min-width:100px;margin-left: 65px;">اسم الخامة المستخدمة : ' . optional($operationOrderDetail)->item_name . '</h5>';
//     //                     $html .= '<h5 style="display:inline-block;max-width:300px;min-width:100px;margin-left: 65px;">كمية الخامة المستخدمة : ' . optional($operationOrderDetail)->old_item_quantity . '</h5>';
//     //                     $html .= '<h5 style="display:inline-block;max-width:300px;min-width:100px;margin-left: 65px;">الخامة الناتجة : ' . $operationOrderDetail->out_item_name . '</h5>';
//     //                 }
//     //                 $html .= '<h5 style="display:inline-block;max-width:300px;min-width:100px;margin-left: 65px;">الطول : ' . $operationOrderDetail->length . '</h5>';
//     //                 $html .= '<h5 style="display:inline-block;max-width:300px;min-width:100px;margin-left: 65px;">العرض : ' . $operationOrderDetail->width . '</h5>';
//     //                 // $html .= '<h5 style="display:inline-block;max-width:300px;min-width:100px;margin-left: 65px;">السعر : '.$operationOrderDetail->price .'</h5>';
//     //                 $html .= '<h5 style="display:inline-block;max-width:300px;min-width:100px;margin-left: 65px;">الكمية : ' . $operationOrderDetail->quantity . '</h5>';
//     //                 if (auth()->user()->can('update_operation_orders')) {
//     //                     $html .= '<a class="btn btn-warning" style="text-decoration: none;" target="_blank" href="' . route("dashboard.operation_orders.edit", $operationOrder->id) . '" ><i class="fa fa-edit"></i> Edit</a>';
//     //                 }
//     //             }
//     //         }
//     //     }

//     //     return response()->json(['html' => $html]);
//     // }
//     public function getOpertOrderWeight(Request $request)
//     {
//         if (isset($request->operation_order_detail_id) && isset($request->actual_output) && isset($request->thickness)) {
//             $weight = '';
//             $total_used_length = '';
//             $operationOrderDetails = OperationOrderDetail::where('id', $request->operation_order_detail_id)->get();
//             $operationOrderDetail = OperationOrderDetail::where('id', $request->operation_order_detail_id)->first();
//             $operationOrder = OperationOrder::where('id', $operationOrderDetail->operation_order_id)->first();
//             $total_used_length = $operationOrder->total_used_length;
//             // dd($operationOrder);
//             foreach ($operationOrderDetails as $operationOrderDetail) {
//                 $weight = 8 * ($operationOrderDetail->length) * ($operationOrderDetail->width) * ($request->actual_output) * ($request->thickness);
//             }
//             return response()->json(['weight' => $weight, 'total_used_length' => $total_used_length]);
//         } else if (isset($request->operation_order_detail_id)) {
//             $weight = '';
//             $total_used_length = '';
//             $operationOrderDetails = OperationOrderDetail::where('id', $request->operation_order_detail_id)->first();
//             $operationOrder = OperationOrder::where('id', $operationOrderDetails->operation_order_id)->first();
//             $total_used_length = $operationOrder->total_used_length;
//             return response()->json(['weight' => $weight, 'total_used_length' => $total_used_length]);
//         } else {
//             $weight = '';
//             $total_used_length = '';

//             return response()->json(['weight' => $weight, 'total_used_length' => $total_used_length]);
//         }
//     }
//     public function getDamageWeight(Request $request)
//     {
//         if (isset($request->operation_order_detail_id) && isset($request->damage_length)  && isset($request->damage_quantity) && isset($request->thickness)) {
//             $weight = '';
//             $operationOrderDetails = OperationOrderDetail::where('id', $request->operation_order_detail_id)->get();
//             // dd($operationOrder);
//             foreach ($operationOrderDetails as $operationOrderDetail) {
//                 $weight = 8 * ($request->damage_length) * ($operationOrderDetail->width) * ($request->damage_quantity) * ($request->thickness);
//             }
//             return response()->json(['weight' => round($weight, 0)]);
//             // return response()->json(['weight' => $weight]);
//         } else {
//             $weight = '';

//             return response()->json(['weight' => $weight]);
//         }
//     }

//     public function updateConfirm(Request $request)
//     {
//         // $counter = 0;
//         $requestConfirm = 0;
//         if(isset($request->resource)) {
//             foreach ($request->resource as $res) {
//                 $resource = OperationOrderResult::where('id', $res['itemId'])->first();
//                 // dd($request->resource);
//                 if ($resource) {
//                     if(array_key_exists('confirmed', $res)) {
//                         if($res['confirmed'] == 'on')
//                             $res['confirmed'] = 1;
//                         else
//                             $res['confirmed'] = 0;

//                         // $dt = new DateTime;
//                         $resource->update([
//                             'confirmed' => $res['confirmed'],
//                             'confirm_notes' => $res['confirm_notes'],
//                             'user_id' => auth()->user()->id,
//                             'confirmed_at' => date('Y-m-d h:i:s'),
//                         ]);
//                     }

//                     if (array_key_exists('store_confirm', $res)) {
//                         if ($res['store_confirm'] == 'on')
//                             $res['store_confirm'] = 1;
//                         else
//                             $res['store_confirm'] = 0;
                        
//                         $resource->update([
//                             'store_confirm' => $res['store_confirm'],
//                             'store_confirm_notes' => $res['store_confirm_notes'],
//                         ]);

//                         $operationOrder = OperationOrder::where('id', $resource->operation_order_id)->first();
//                         $machine = Machines::where('id', $operationOrder->machine_id)->first();
//                         $store = DB::table('stores')
//                             ->select('id', 'name')
//                             ->where('id', $machine->store_id)
//                             ->first();
//                         $orderDetail = OperationOrderDetail::where('id', $resource->order_details_id)->where('operation_order_id', $operationOrder->id)->first();
//                         // return $orderDetail;
//                         DB::beginTransaction();

//                         //decrement old item quntity
//                         if ($resource->old_item_quantity > 0) {
//                             $quantity = Quantity::where('ownerable_type', 'App\Models\Store')
//                                 ->where('ownerable_id', $store->id)
//                                 ->where('item_id', $orderDetail->item_id)
//                                 ->first();


//                             $quantity->decrement('quantity', $resource->old_item_quantity);
//                             $quantity->save();

//                             $orderDetail->new_in_balance = $quantity->quantity;
//                             $orderDetail->save();
//                         }
//                         if ($resource->actual_output > 0) {
//                             if (isset($orderDetail->out_item_id)) {
//                                 $quantity = Quantity::where('ownerable_type', 'App\Models\Store')
//                                     ->where('ownerable_id', $store->id)
//                                     ->where('item_id', $orderDetail->out_item_id)
//                                     ->first();


//                                 $quantity->increment('quantity', $resource->actual_output);
//                                 $quantity->save();

//                                 $orderDetail->new_out_balance = $quantity->quantity;
//                                 $orderDetail->save();
//                             } else {
//                                 if ($orderDetail->is_special == 1) {    //special
//                                     $specail = Special::create([        //create in item as special
//                                         'price' => $orderDetail->price,
//                                         'name' => $orderDetail->out_name,
//                                         'length' => $orderDetail->length,
//                                         'width' => $orderDetail->width,
//                                         'weight' => $resource->weight,
//                                         'group_id' => $orderDetail->out_group_id,
//                                         'is_special' => 1,
//                                         'operat_ord_id' => $operationOrder->id,
//                                     ]);
//                                     $specail->code = $specail->id;
//                                     $specail->save();

//                                     $quantity = Quantity::create([                  //create in quantity
//                                         'ownerable_type'    => 'App\Models\Store',
//                                         'ownerable_id' => $store->id,
//                                         'item_id' => $specail->id,
//                                         'quantity' => $resource->actual_output
//                                     ]);
//                                 }
//                             }
//                         }

//                         $orderResultDetails = OperationOrderResultDetail::where('order_results_id', $resource->id)->get();

//                         foreach ($orderResultDetails as $orderResultDetail) {
//                             if ($orderResultDetail->damage_quantity > 0) {
//                                 if (isset($orderResultDetail->old_damage_id)) {
//                                     $quantity = Quantity::where('ownerable_type', 'App\Models\Store')
//                                         ->where('ownerable_id', $store->id)
//                                         ->where('item_id', $orderResultDetail->old_damage_id)
//                                         ->first();
//                                     if ($orderResultDetail->damage_type == 'scrap')
//                                         $quantity->increment('quantity', $orderResultDetail->damage_weight);
//                                     else
//                                         $quantity->increment('quantity', $orderResultDetail->damage_quantity);

//                                     $quantity->save();
//                                 } else {
//                                     if ($orderResultDetail->damage_type == 'scrap')
//                                         $damageGroup = DB::table('groups')->where('name', 'خرده')->first();
//                                     else
//                                         $damageGroup = DB::table('groups')->where('name', 'Damage')->first();

//                                     $damage = Damage::create([        //create in item as damage
//                                         'price' => $orderResultDetail->damage_price,
//                                         'name' => $orderResultDetail->damage_name,
//                                         'is_damage' => 1,
//                                         'length' => $orderResultDetail->damage_length,
//                                         'width' => $orderResultDetail->damage_width,
//                                         'weight' => $orderResultDetail->damage_weight,
//                                         'damage_type' => $orderResultDetail->damage_type,
//                                         'group_id' => $damageGroup->id,
//                                         'operat_ord_id' => $operationOrder->id,
//                                     ]);

//                                     $damage->code = $damage->id;
//                                     $damage->save();

//                                     $quantity = Quantity::create([                  //create in quantity
//                                         'ownerable_type'    => 'App\Models\Store',
//                                         'ownerable_id' => $store->id,
//                                         'item_id' => $damage->id,
//                                         'quantity' => $orderResultDetail->damage_quantity
//                                     ]);
//                                     //decrement the damage quantity from old item quantity
//                                 }
//                                 $oldItemQuantity = Quantity::where('ownerable_type', 'App\Models\Store')
//                                     ->where('ownerable_id', $store->id)
//                                     ->where('item_id', $orderDetail->item_id)
//                                     ->first();
//                                 $oldItemQuantity->decrement('quantity', $orderResultDetail->damage_weight);
//                                 $oldItemQuantity->save();
//                             }
//                         }

//                         DB::commit();
//                     }

//                     if(array_key_exists('confirm_notes', $res)) {
//                         $resource->update([
//                             'confirm_notes' => $res['confirm_notes'],
//                         ]);
//                     }

//                     if(array_key_exists('store_confirm_notes', $res)) {
//                         $resource->update([
//                             'store_confirm_notes' => $res['store_confirm_notes'],
//                         ]);
//                     }

//                     if(array_key_exists('store_employees', $res)) {
//                         $employees = $res['store_employees'];
//                         $resource->update([
//                             'store_employees' => implode(',', $employees),
//                         ]);
//                     }
//                 }

//                 // $counter++;
//             }
//         } else {
//             session()->flash('error', 'بالرجاء تحديد ناتج أمر شغل للتأكيد');
//             return redirect()->back();
//         }
//         session()->flash('success', __('site.confirmed_successfully'));
//         return redirect()->back();
//     }
//     //
    
//     public function updateConfirmOut(Request $request)
//     {
//         // $counter = 0;
//         $requestConfirm = 0;
//         if(isset($request->resource)) {
//             foreach ($request->resource as $res) {
//                 $resource = OperationOrderResult::where('id', $res['itemId'])->first();
//                 // dd($request->resource);
//                 if($resource) {
//                     if(array_key_exists('confirmed', $res)) {
//                         if ($res['confirmed'] == 'on')
//                             $res['confirmed'] = 1;
//                         else
//                             $res['confirmed'] = 0;

//                         $resource->update([
//                             'confirmed' => $res['confirmed'],
//                             'confirm_notes' => $res['confirm_notes'],
//                             'user_id' => auth()->user()->id,
//                             'confirmed_at' => date('Y-m-d h:i:s'),
//                         ]);

//                         $operationOrder = OperationOrder::where('id', $resource->operation_order_id)->first();
//                         $operationOrder->update([
//                             'out_confirmed' => $res['confirmed'],
//                             'confirm_notes' => $res['confirm_notes'],
//                             'confirm_user_id' => auth()->user()->id,
//                             'confirmed_at' => date('Y-m-d h:i:s'),
//                         ]);
//                     }

//                     if(array_key_exists('store_confirm', $res)) {
//                         if ($res['store_confirm'] == 'on')
//                             $res['store_confirm'] = 1;
//                         else
//                             $res['store_confirm'] = 0;

//                         $operationOrder = OperationOrder::where('id', $resource->operation_order_id)->first();
//                         $machine = Machines::where('id', $operationOrder->machine_id)->first();
//                         $store = DB::table('stores')
//                             ->select('id', 'name')
//                             ->where('id', $machine->store_id)
//                             ->first();

//                         $operationOrder = OperationOrder::where('id', $resource->operation_order_id)->first();
//                         $orderResultDetails = OperationOrderResultDetail::where('order_results_id', $resource->id)->get();
//                         DB::beginTransaction();

//                         foreach ($orderResultDetails as $orderResultDetail) {
//                             if ($orderResultDetail->damage_quantity > 0) {
//                                 if (isset($orderResultDetail->old_damage_id)) {
//                                     $quantity = Quantity::where('ownerable_type', 'App\Models\Store')
//                                         ->where('ownerable_id', $store->id)
//                                         ->where('item_id', $orderResultDetail->old_damage_id)
//                                         ->first();
//                                     $quantity->increment('quantity', $orderResultDetail->damage_weight);
//                                     $quantity->save();
//                                 } else {
//                                     $damageGroup = DB::table('groups')->where('name', 'خرده')->first();
//                                     $damage = Damage::create([        //create in item as damage
//                                         'price' => $orderResultDetail->damage_price,
//                                         'name' => $orderResultDetail->damage_name,
//                                         'is_damage' => 1,
//                                         'length' => $orderResultDetail->damage_length,
//                                         'width' => $orderResultDetail->damage_width,
//                                         'weight' => $orderResultDetail->damage_weight,
//                                         'damage_type' => $orderResultDetail->damage_type,
//                                         'group_id' => $damageGroup->id,
//                                         'operat_ord_id' => $operationOrder->id,
//                                     ]);

//                                     $damage->code = $damage->id;
//                                     $damage->save();

//                                     $quantity = Quantity::create([
//                                         'ownerable_type'    => 'App\Models\Store',
//                                         'ownerable_id' => $store->id,
//                                         'item_id' => $damage->id,
//                                         'quantity' => $orderResultDetail->damage_quantity
//                                     ]);
//                                     //decrement the damage quantity from old item quantity
//                                 }
//                             }
//                         }

//                         $resource->update([
//                             'store_confirm' => $res['store_confirm'],
//                             'store_confirm_notes' => $res['store_confirm_notes'],
//                         ]);

//                         DB::commit();
//                     }

//                     if(array_key_exists('confirm_notes', $res)) {
//                         $resource->update([
//                             'confirm_notes' => $res['confirm_notes'],
//                         ]);
//                     }

//                     if(array_key_exists('store_confirm_notes', $res)) {
//                         $resource->update([
//                             'store_confirm_notes' => $res['store_confirm_notes'],
//                         ]);
//                     }

//                     if(array_key_exists('store_employees', $res)) {
//                         $employees = $res['store_employees'];
//                         $resource->update([
//                             'store_employees' => implode(',', $employees),
//                         ]);
//                     }
//                 }

//                 // $counter++;
//             }
//         } else {
//             session()->flash('error', 'بالرجاء تحديد ناتج أمر شغل للتأكيد');
//             return redirect()->back();
//         }
//         session()->flash('success', __('site.confirmed_successfully'));
//         return redirect()->back();
//     }
//     //

//     // public function updateConfirm(Request $request)
//     // {
//     //     // dd($request->all());
//     //     // $counter = 0;
//     //     $requestConfirm = 0;
//     //     foreach ($request->resource as $res) {
//     //         $resource = OperationOrderResult::where('id', $res['itemId'])->first();
//     //         // dd($request->resource);
//     //         if ($resource) {
//     //             if (array_key_exists('confirmed', $res)) {
//     //                 if ($res['confirmed'] == 'on')
//     //                     $res['confirmed'] = 1;
//     //                 else
//     //                     $res['confirmed'] = 0;

//     //                 // $dt = new DateTime;
//     //                 $resource->update([
//     //                     'confirmed' => $res['confirmed'],
//     //                     'confirm_notes' => $res['confirm_notes'],
//     //                     'user_id' => auth()->user()->id,
//     //                     'confirmed_at' => date('Y-m-d h:i:s'),
//     //                 ]);

//     //                 $operationOrder = OperationOrder::where('id', $resource->operation_order_id)->first();
//     //                 $machine = Machines::where('id', $operationOrder->machine_id)->first();
//     //                 $store = DB::table('stores')
//     //                     ->select('id', 'name')
//     //                     ->where('id', $machine->store_id)
//     //                     ->first();
//     //                 $orderDetail = OperationOrderDetail::where('id', $resource->order_details_id)->where('operation_order_id', $operationOrder->id)->first();

//     //                 DB::beginTransaction();

//     //                 //decrement old item quntity
//     //                 if ($resource->old_item_quantity > 0) {
//     //                     $quantity = Quantity::where('ownerable_type', 'App\Models\Store')
//     //                         ->where('ownerable_id', $store->id)
//     //                         ->where('item_id', $orderDetail->item_id)
//     //                         ->first();


//     //                     $quantity->decrement('quantity', $resource->old_item_quantity);
//     //                     $quantity->save();

//     //                     $orderDetail->new_in_balance = $quantity->quantity;
//     //                     $orderDetail->save();
//     //                 }
//     //                 if ($resource->actual_output > 0) {
//     //                     if (isset($orderDetail->out_item_id)) {
//     //                         $quantity = Quantity::where('ownerable_type', 'App\Models\Store')
//     //                             ->where('ownerable_id', $store->id)
//     //                             ->where('item_id', $orderDetail->out_item_id)
//     //                             ->first();


//     //                         $quantity->increment('quantity', $resource->actual_output);
//     //                         $quantity->save();

//     //                         $orderDetail->new_out_balance = $quantity->quantity;
//     //                         $orderDetail->save();
//     //                     } else {
//     //                         if ($orderDetail->is_special == 1) {    //special
//     //                             $specail = Special::create([        //create in item as special
//     //                                 'price' => $orderDetail->price,
//     //                                 'name' => $orderDetail->out_name,
//     //                                 'length' => $orderDetail->length,
//     //                                 'width' => $orderDetail->width,
//     //                                 'weight' => $resource->weight,
//     //                                 'group_id' => $orderDetail->out_group_id,
//     //                                 'is_special' => 1,
//     //                                 'operat_ord_id' => $operationOrder->id,
//     //                             ]);
//     //                             $specail->code = $specail->id;
//     //                             $specail->save();

//     //                             $quantity = Quantity::create([                  //create in quantity
//     //                                 'ownerable_type'    => 'App\Models\Store',
//     //                                 'ownerable_id' => $store->id,
//     //                                 'item_id' => $specail->id,
//     //                                 'quantity' => $resource->actual_output
//     //                             ]);
//     //                         }
//     //                     }
//     //                 }

//     //                 $orderResultDetails = OperationOrderResultDetail::where('order_results_id', $resource->id)->get();

//     //                 foreach ($orderResultDetails as $orderResultDetail) {
//     //                     if ($orderResultDetail->damage_quantity > 0) {
//     //                         if (isset($orderResultDetail->old_damage_id)) {
//     //                             $quantity = Quantity::where('ownerable_type', 'App\Models\Store')
//     //                                 ->where('ownerable_id', $store->id)
//     //                                 ->where('item_id', $orderResultDetail->old_damage_id)
//     //                                 ->first();
//     //                             $quantity->increment('quantity', $orderResultDetail->damage_quantity);
//     //                             $quantity->save();
//     //                         } else {
//     //                             $damageGroup = DB::table('groups')->where('name', 'Damage')->first();

//     //                             $damage = Damage::create([        //create in item as damage
//     //                                 'price' => $orderResultDetail->damage_price,
//     //                                 'name' => $orderResultDetail->damage_name,
//     //                                 'is_damage' => 1,
//     //                                 'length' => $orderResultDetail->damage_length,
//     //                                 'width' => $orderResultDetail->damage_width,
//     //                                 'weight' => $orderResultDetail->damage_weight,
//     //                                 'damage_type' => $orderResultDetail->damage_type,
//     //                                 'group_id' => $damageGroup->id,
//     //                                 'operat_ord_id' => $operationOrder->id,
//     //                             ]);

//     //                             $damage->code = $damage->id;
//     //                             $damage->save();

//     //                             $quantity = Quantity::create([                  //create in quantity
//     //                                 'ownerable_type'    => 'App\Models\Store',
//     //                                 'ownerable_id' => $store->id,
//     //                                 'item_id' => $damage->id,
//     //                                 'quantity' => $orderResultDetail->damage_quantity
//     //                             ]);

//     //                             //decrement the damage quantity from old item quantity
//     //                             $oldItemQuantity = Quantity::where('ownerable_type', 'App\Models\Store')
//     //                                 ->where('ownerable_id', $store->id)
//     //                                 ->where('item_id', $orderDetail->item_id)
//     //                                 ->first();
//     //                             $oldItemQuantity->decrement('quantity', $orderResultDetail->damage_weight);
//     //                             $oldItemQuantity->save();
//     //                         }
//     //                     }
//     //                 }

//     //                 DB::commit();
//     //             }
//     //             if (array_key_exists('confirm_notes', $res)) {
//     //                 // $dt = new DateTime;
//     //                 $resource->update([
//     //                     'confirm_notes' => $res['confirm_notes'],
//     //                 ]);
//     //             }
//     //         }

//     //         // $counter++;
//     //     }
//     //     session()->flash('success', __('site.confirmed_successfully'));
//     //     return redirect()->back();
//     // }

//     // public function updateConfirmOut(Request $request)
//     // {
//     //     // dd($request->all());
//     //     // $counter = 0;
//     //     $requestConfirm = 0;
//     //     foreach ($request->resource as $res) {
//     //         $resource = OperationOrderResult::where('id', $res['itemId'])->first();
//     //         // dd($request->resource);
//     //         if ($resource) {
//     //             if (array_key_exists('confirmed', $res)) {
//     //                 if ($res['confirmed'] == 'on')
//     //                     $res['confirmed'] = 1;
//     //                 else
//     //                     $res['confirmed'] = 0;

//     //                 // $dt = new DateTime;


//     //                 $operationOrder = OperationOrder::where('id', $resource->operation_order_id)->first();

//     //                 DB::beginTransaction();

//     //                 $resource->update([
//     //                     'confirmed' => $res['confirmed'],
//     //                     'confirm_notes' => $res['confirm_notes'],
//     //                     'user_id' => auth()->user()->id,
//     //                     'confirmed_at' => date('Y-m-d h:i:s'),
//     //                 ]);

//     //                 $operationOrder->update([
//     //                     'out_confirmed' => $res['confirmed'],
//     //                     'confirm_notes' => $res['confirm_notes'],
//     //                     'confirm_user_id' => auth()->user()->id,
//     //                     'confirmed_at' => date('Y-m-d h:i:s'),
//     //                 ]);

//     //                 DB::commit();
//     //             }
//     //             if (array_key_exists('confirm_notes', $res)) {
//     //                 // $dt = new DateTime;
//     //                 $resource->update([
//     //                     'confirm_notes' => $res['confirm_notes'],
//     //                 ]);
//     //                 // $operationOrder->update([
//     //                 //     'confirm_notes' => $res['confirm_notes'],
//     //                 // ]);
//     //             }
//     //         }

//     //         // $counter++;
//     //     }
//     //     session()->flash('success', __('site.confirmed_successfully'));
//     //     return redirect()->back();
//     // }

//     public function push_notification($message)
//     {
//         $options = array(
//             'cluster' => 'eu',
//             'useTLS' => true
//         );
//         $pusher = new Pusher(
//             'e75d58425f4b10f93cfb',
//             '49edd2fdb43527c84354',
//             '417914',
//             $options
//         );
//         $data['message'] = $message;
//         $pusher->trigger('my-channel', 'my-event', $data);
//         return true;
//     }
    
//         public function auto_complete_first(Request $request)
//     {
//         $term = $request->term;
//         $results = [];

//         if ($term) {
//             $results = Damage::where('name', 'LIKE', '%' . $term . '%')
//                 ->where('is_damage', 1)->where('group_id', 63)
//                 ->pluck('name'); // Replace with your actual column name
//         }

//         return response()->json($results);
//     }
//     public function auto_complete_second(Request $request)
//     {
//         $term = $request->term;
//         $results = [];

//         if ($term) {
//             $results = Damage::where('name', 'LIKE', '%' . $term . '%')
//                 ->where('is_damage', 1)->where('group_id', 71)
//                 ->pluck('name'); // Replace with your actual column name
//         }


//         return response()->json($results);
//     }
// }


//------------------------------------------------------------------------------------------------- ~~ -------------------------------------------------------------------------------------------------------------------------//



// namespace App\Http\Controllers\Dashboard;

// use App\Department;
// use App\Student;
// use App\Admin;
// use App\Reposite;
// use App\SupplieTypes;
// use App\Supplies;
// use App\OperationOrder;
// use App\OperationOrderDetail;
// use App\OperationOrderResult;
// use App\OperationOrderResultDetail;
// use App\Item;
// use App\Damage;
// use App\Employee;
// use App\Special;
// use App\Quantity;
// use App\MachineSupplie;
// use App\Machines;
// use Illuminate\Http\Request;
// use App\Http\Controllers\Controller;
// use App\User;
// use Illuminate\Support\Facades\DB;
// use Illuminate\Validation\Rule;
// use Maatwebsite\Excel\Facades\Excel;
// use Yajra\DataTables\Facades\DataTables as FacadesDataTables;
// use Storage;
// use Pusher\Pusher;
// use Illuminate\Support\Arr;

// class OperationOrderResultController extends Controller
// {
//     public function __construct(){
//         $this->middleware(['ability:admin,reade_operation_order_results'])->only('index');
//         $this->middleware(['ability:admin,create_operation_order_results'])->only('create');
//         $this->middleware(['ability:admin,update_operation_order_results'])->only('edit');
//         $this->middleware(['ability:admin,delete_operation_order_results'])->only('destroy');

//     }


//     public function getData(Request $request) {
//         $query = OperationOrderResult::query();

//         // if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('مسئول فرع و مصنع' || auth()->user()->hasRole('مسئول مصنع') || auth()->user()->hasRole('مسئول مخزن و مصنع'))){
//         if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('branch_factor_respons') || auth()->user()->hasRole('store_factor_response') || auth()->user()->hasRole('factor_response')){
//             $query->whereHas('operationOrder', function($q){
//                 $q->where('out_operation', 0);
//             })->get();
//         }else{
//             $query = OperationOrderResult::toUser()->whereHas('operationOrder', function($q){
//                 $q->where('out_operation', 0);
//             });
//         }

//         return FacadesDataTables::eloquent($query->with(['operationOrder','operationOrder.supervisor' ,'user'])->latest())
//                         ->addColumn('action', function(OperationOrderResult $operationOrderResult) {
//                             $type = "action";
//                             return view("dashboard.operation_order_results.action", compact("operationOrderResult", "type"));
//                         })
//                         ->addColumn('confirm_notes', function(OperationOrderResult $operationOrderResult) {
//                             $type = "confirm_notes";
//                             return view("dashboard.operation_order_results.action", compact("operationOrderResult", "type"));
//                         })
//                         ->editColumn('operation_order_id',function(OperationOrderResult $operationOrderResult){
//                             return '<a style="text-decoration: none;" target="_blank" href="'.route("dashboard.operation_orders.show", $operationOrderResult->operationOrder->id).'" >'.$operationOrderResult->operationOrder->id.'</a>';
//                         })
//                         ->editColumn('date',function(OperationOrderResult $operationOrderResult){
//                             return optional($operationOrderResult->operationOrder)->date;
//                         })
//                         ->editColumn('supervisor_name',function(OperationOrderResult $operationOrderResult){
//                             return optional(optional($operationOrderResult->operationOrder)->supervisor)->name;
//                         })
//                         ->editColumn('machine_name', function (OperationOrderResult $operationOrderResult) {
//                             return optional(optional($operationOrderResult->operationOrder)->machine)->name;
//                         })
//                         ->addColumn('suplies_name',function(OperationOrderResult $operationOrderResult){
//                             $operationSupliesIds = explode(',', $operationOrderResult->operationOrderDetail->operation_suplies_id);
//                             $operationSupliesNames = Supplies::whereIn('id', $operationSupliesIds)->pluck('name')->toArray();
//                             return $operationSupliesNames;
//                         })
//                         ->editColumn('user_id',function(OperationOrderResult $operationOrderResult){
//                             if($operationOrderResult->user_id){
//                                 $name =optional($operationOrderResult->user)->name;

//                                 return ' '.$name. '<br>'. $operationOrderResult->confirmed_at;

//                             }
//                             return '';
//                         })
//                         ->rawColumns(['action', 'confirm_notes','date','user_id', 'operation_order_id','supervisor_name', 'suplies_name'])
//                         ->toJson();
//     }

//     public function getDataOut(Request $request) {
//         $query = OperationOrderResult::query();

//         // if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('مسئول فرع و مصنع' || auth()->user()->hasRole('مسئول مصنع') || auth()->user()->hasRole('مسئول مخزن و مصنع'))){
//         if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('branch_factor_respons') || auth()->user()->hasRole('store_factor_response') || auth()->user()->hasRole('factor_response')){
//             $query->whereHas('operationOrder', function($q){
//                 $q->where('out_operation', 1);
//             })->get();
//         }else{
//             $query = OperationOrderResult::toUser()->whereHas('operationOrder', function($q){
//                 $q->where('out_operation', 1);
//             });
//         }

//         return FacadesDataTables::eloquent($query->with(['operationOrder','operationOrder.supervisor' ,'user'])->latest())
//                         ->addColumn('action', function(OperationOrderResult $operationOrderResult) {
//                             $type = "action";
//                             return view("dashboard.operation_order_results.action_out", compact("operationOrderResult", "type"));
//                         })
//                         ->addColumn('confirm_notes', function(OperationOrderResult $operationOrderResult) {
//                             $type = "confirm_notes";
//                             return view("dashboard.operation_order_results.action_out", compact("operationOrderResult", "type"));
//                         })
//                         ->editColumn('operation_order_id',function(OperationOrderResult $operationOrderResult){
//                             return '<a style="text-decoration: none;" target="_blank" href="'.route("dashboard.operation_orders.show", $operationOrderResult->operationOrder->id).'" >'.$operationOrderResult->operationOrder->id.'</a>';
//                         })
//                         ->editColumn('date',function(OperationOrderResult $operationOrderResult){
//                             return optional($operationOrderResult->operationOrder)->date;
//                         })
//                         ->editColumn('supervisor_name',function(OperationOrderResult $operationOrderResult){
//                             return optional(optional($operationOrderResult->operationOrder)->supervisor)->name;
//                         })
//                         ->editColumn('machine_name', function (OperationOrderResult $operationOrderResult) {
//                             return optional(optional($operationOrderResult->operationOrder)->machine)->name;
//                         })
//                         ->addColumn('suplies_name',function(OperationOrderResult $operationOrderResult){
//                             $operationSupliesIds = explode(',', $operationOrderResult->operationOrderDetail->operation_suplies_id);
//                             $operationSupliesNames = Supplies::whereIn('id', $operationSupliesIds)->pluck('name')->toArray();
//                             return $operationSupliesNames;
//                         })
//                         ->editColumn('user_id',function(OperationOrderResult $operationOrderResult){
//                             if($operationOrderResult->user_id){
//                                 $name =optional($operationOrderResult->user)->name;

//                                 return ' '.$name. '<br>'. $operationOrderResult->confirmed_at;

//                             }
//                             return '';
//                         })
//                         ->rawColumns(['action', 'confirm_notes','date','user_id', 'operation_order_id','supervisor_name', 'suplies_name'])
//                         ->toJson();
//     }

//     public function index(Request $request)
//     {
//         return view('dashboard.operation_order_results.index');
//     }

//     public function indexOut(Request $request)
//     {
//         return view('dashboard.operation_order_results.index_out');
//     }

//     /**
//      * Momaher
//      */
//     public function show(OperationOrderResult $operationOrderResult)
//     {
//         $operationSupliesIds = explode(',', $operationOrderResult->operationOrderDetail->operation_suplies_id);
//         $supplies = Supplies::whereIn('id', $operationSupliesIds)->pluck('name')->toArray();
//         $machine_id = $operationOrderResult->operationOrder->machine_id;
//         $used = MachineSupplie::select(DB::raw("SUM(used) as used"))
//             ->where('machine_id', $machine_id)
//             ->whereIn('supplie_id', $operationSupliesIds)
//             ->groupBy('supplie_id')
//             ->pluck('used');
//         $quantity = $operationOrderResult->operationOrderDetail->quantity;
//         return view('dashboard.operation_order_results.show', compact('operationOrderResult', 'supplies', 'quantity', 'used'));
//     }

//     public function showOut(OperationOrderResult $operationOrderResult)
//     {
//         $operationSupliesIds = explode(',', $operationOrderResult->operationOrderDetail->operation_suplies_id);
//         $supplies = Supplies::whereIn('id', $operationSupliesIds)->pluck('name')->toArray();
//         $machine_id = $operationOrderResult->operationOrder->machine_id;
//         $used = MachineSupplie::select(DB::raw("SUM(used) as used"))
//             ->where('machine_id', $machine_id)
//             ->whereIn('supplie_id', $operationSupliesIds)
//             ->groupBy('supplie_id')
//             ->pluck('used');
//         $quantity = $operationOrderResult->operationOrderDetail->quantity;
//         $client = $operationOrderResult->operationOrder->client_name;
//         return view('dashboard.operation_order_results.show_out', compact('operationOrderResult', 'supplies', 'quantity', 'client', 'used'));
//     }
//     /**
//      * Momaher
//      */

//     // public function show(OperationOrderResult $operationOrderResult)
//     // {

//     //     return view('dashboard.operation_order_results.show', compact('operationOrderResult'));
//     // }

//     // public function showOut(OperationOrderResult $operationOrderResult)
//     // {

//     //     return view('dashboard.operation_order_results.show_out', compact('operationOrderResult'));
//     // }

//     public function create()
//     {
//         // if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('branch_factor_respons') || auth()->user()->hasRole('store_factor_response') || auth()->user()->hasRole('factor_response')){
//         //     $operationOrders = OperationOrder::latest()->get();
//         // }else{
//         //     $operationOrders = OperationOrder::toUser();
//         // }

//         if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('branch_factor_respons') || auth()->user()->hasRole('store_factor_response') || auth()->user()->hasRole('factor_response')){
//             $operationOrders = OperationOrder::where('out_operation', 0)->latest()->get();
//             $operationOrdrDetails = OperationOrderDetail::whereHas('operationOrder', function($q){
//                 $q->where('out_operation', 0);
//             })->latest()->get();

//         }else{
//             $operationOrders = OperationOrder::toUser()->where('out_operation', 0)->latest()->get();
//             $operationOrdersIds= $operationOrders->pluck('id')->toArray();
//             $operationOrdrDetails = OperationOrderDetail::whereIn('operation_order_id',$operationOrdersIds)->latest()->get();
//         }
//                     // dd($operationOrders);
//         $damages = Damage::where('is_damage', 1)->latest()->get();
//         return view('dashboard.operation_order_results.create', compact('operationOrders', 'operationOrdrDetails', 'damages'));
//     }

//     public function createOut()
//     {
//         // if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('branch_factor_respons') || auth()->user()->hasRole('store_factor_response') || auth()->user()->hasRole('factor_response')){
//         //     $operationOrders = OperationOrder::latest()->get();
//         // }else{
//         //     $operationOrders = OperationOrder::toUser();
//         // }

//         if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('branch_factor_respons') || auth()->user()->hasRole('store_factor_response') || auth()->user()->hasRole('factor_response')){
//             $operationOrders = OperationOrder::where('out_operation', 1)->latest()->get();
//             $operationOrdrDetails = OperationOrderDetail::whereHas('operationOrder', function($q){
//                 $q->where('out_operation', 1);
//             })->latest()->get();

//         }else{
//             $operationOrders = OperationOrder::toUser()->where('out_operation', 1)->latest()->get();
//             $operationOrdersIds= $operationOrders->pluck('id')->toArray();
//             $operationOrdrDetails = OperationOrderDetail::whereIn('operation_order_id',$operationOrdersIds)->latest()->get();
//         }
//                     // dd($operationOrders);
//         $damages = Damage::where('is_damage', 1)->latest()->get();
//         return view('dashboard.operation_order_results.create_out', compact('operationOrders', 'operationOrdrDetails', 'damages'));
//     }

//     public function store(Request $request)
//     {

//         $request->validate([
//             'order_details_id' => 'required',
//             'actual_output' => 'required',
//             'old_item_quantity' => 'required',
//             'notes' ,
//         ]);

//         $request_data = $request->except(['damage_type','old_damage_id','damage_quantity','damage_length','damage_width','damage_thickness','damage_price','damage_weight']);

//         $request_data['operation_order_id'] = OperationOrderDetail::where('id', $request->order_details_id)->first()->operation_order_id;
//         $request_data['order_details_id'] = $request->order_details_id;
//         $operationOrderResult = OperationOrderResult::create($request_data);
//         $operationOrder = OperationOrder::where('id', $operationOrderResult->operation_order_id)->first();
//         $operationOrderDetail = OperationOrderDetail::where('id', $operationOrderResult->order_details_id)->first();

//         $machine = Machines::where('id',$operationOrder->machine_id)->first();
//         $store = DB::table('stores')
//                     ->select('id','name')
//                     ->where('id', $machine->store_id)
//                     ->first();

//         if($request->damage_quantity){
//             $counter=0;
//             foreach ($request->damage_quantity as $item) {
//                 $orderResultDetail = OperationOrderResultDetail::create([
//                     'operation_order_id'        => $operationOrderResult->operation_order_id,
//                     'order_details_id'      => $operationOrderResult->order_details_id,
//                     'order_results_id'      => $operationOrderResult->id,
//                     'damage_type'       => $request->damage_type[$counter],
//                     'damage_name'       => $request->damage_name[$counter],
//                     'damage_quantity'       => $request->damage_quantity[$counter],
//                     'damage_length'     => $request->damage_length[$counter],
//                     'damage_width'      => $operationOrderDetail->width,
//                     'damage_thickness'      => $operationOrderResult->thickness,
//                     'damage_price'      => $request->damage_price[$counter],
//                     'damage_weight'     => $request->damage_weight[$counter],
//                 ]);
//                 if($request->old_damage_id != null){
//                     $orderResultDetail->old_damage_id = $request->old_damage_id[$counter];
//                     $orderResultDetail->save();
//                 }
//                 $counter++;
//             }
//         }

//         //send notifications to Responsable users of machine branch

//         $machineBranch = DB::table('branches')->where('name', $store->name)->first();
//         $rolesIds = DB::table('roles')->whereIn('name', ['factor_response', 'branch_factor_respons'])->pluck('id')->toArray();
//         $usersRespons = DB::table('users')->where('branch_id', $machineBranch->id)->whereIn('role_id', $rolesIds)->pluck('id')->toArray();

//         for($index = 0; $index < count($usersRespons); $index ++) {
//             $this->push_notification(['user_id' => $usersRespons[$index],'url'=>url('operation_orders')]);
//         }

//         session()->flash('success', __('site.added_successfully'));
//         return redirect()->route('dashboard.operation_order_results.index');
//     }

//     public function storeOut(Request $request)
//     {

//         $request->validate([
//             'order_details_id' => 'required',
//             'actual_output' => 'required',
//             'old_item_quantity' => 'required',
//             'total_used_length' => 'required',
//             'notes' ,
//         ]);

//         $request_data = $request->except(['damage_type', 'total_used_length','old_damage_id','damage_quantity','damage_length','damage_width','damage_thickness','damage_price','damage_weight']);
//         // dd($request_data);
//         $request_data['operation_order_id'] = OperationOrderDetail::where('id', $request->order_details_id)->first()->operation_order_id;
//         $request_data['order_details_id'] = $request->order_details_id;
//         $operationOrderResult = OperationOrderResult::create($request_data);
//         $operationOrder = OperationOrder::where('id', $operationOrderResult->operation_order_id)->first();
//         $operationOrder->total_used_length = $request->total_used_length;
//         $operationOrder->save();

//         $machine = Machines::where('id',$operationOrder->machine_id)->first();
//         $store = DB::table('stores')
//                     ->select('id','name')
//                     ->where('id', $machine->store_id)
//                     ->first();


//         //send notifications to Responsable users of machine branch

//         $machineBranch = DB::table('branches')->where('name', $store->name)->first();
//         $rolesIds = DB::table('roles')->whereIn('name', ['factor_response', 'branch_factor_respons'])->pluck('id')->toArray();
//         $usersRespons = DB::table('users')->where('branch_id', $machineBranch->id)->whereIn('role_id', $rolesIds)->pluck('id')->toArray();

//         for($index = 0; $index < count($usersRespons); $index ++) {
//             $this->push_notification(['user_id' => $usersRespons[$index],'url'=>url('operation_orders')]);
//         }

//         session()->flash('success', __('site.added_successfully'));
//         return redirect()->route('dashboard.operation_order_results.index_out');
//     }

//     public function edit($operationOrderResult)
//     {
//         $operationOrders = OperationOrder::latest()->get();
//         $operationOrderResult = OperationOrderResult::find($operationOrderResult);
//         $operationOrdersIds= $operationOrders->pluck('id')->toArray();
//         $operationOrdrDetails = OperationOrderDetail::whereIn('operation_order_id',$operationOrdersIds)->latest()->get();


//         return view('dashboard.operation_order_results.edit', compact('operationOrderResult','operationOrdrDetails','operationOrders'));
//     }

//     public function update(Request $request, $operationOrderResult)
//     {
//         $request->validate([
//             'order_details_id' => 'required',
//             'actual_output' => 'required',
//             'notes' ,
//         ]);
//         $request_data = $request->except(['damage_type','old_damage_id','damage_quantity','damage_length','damage_width','damage_thickness','damage_price','damage_weight']);

//         $operationOrderResult = OperationOrderResult::find($operationOrderResult);
//         $operationOrder = OperationOrder::where('id', $operationOrderResult->operation_order_id)->first();
//         $operationOrderDetail = OperationOrderDetail::where('id', $operationOrderResult->order_details_id)->first();

//         $machine = Machines::where('id',$operationOrder->machine_id)->first();
//         $store = DB::table('stores')
//                         ->select('id','name')
//                         ->where('id', $machine->store_id)
//                         ->first();

//         if($operationOrderDetail->out_item_id){
//             $quantity = Quantity::where('ownerable_type', 'App\Models\Store')
//                                 ->where('ownerable_id', $store->id)
//                                 ->where('item_id', $operationOrderDetail->out_item_id)
//                                 ->first();
//             if($operationOrderResult->confirmed){
//                 $quantity->decrement('quantity', $operationOrderResult->actual_output);
//                 $quantity->save();
//             }
//         }
//         if($operationOrderDetail->item_id){
//             $quantity = Quantity::where('ownerable_type', 'App\Models\Store')
//                                 ->where('ownerable_id', $store->id)
//                                 ->where('item_id', $operationOrderDetail->item_id)
//                                 ->first();
//             if($operationOrderResult->confirmed){
//                 $quantity->increment('quantity', $operationOrderResult->old_item_quantity);
//                 $quantity->save();
//             }

//         }
//         $request_data['confirmed'] = 0;
//         $operationOrderResult->update($request_data);

//         session()->flash('success', __('site.updated_successfully'));
//         return redirect()->route('dashboard.operation_order_results.index');
//     }


//     public function destroy($operationOrderResult)
//     {
//         $operationOrderResult = OperationOrderResult::find($operationOrderResult);
//         $operationOrder = OperationOrder::where('id', $operationOrderResult->operation_order_id)->first();
//         $operationOrderDetail = OperationOrderDetail::where('id', $operationOrderResult->order_details_id)->first();

//         $machine = Machines::where('id',$operationOrder->machine_id)->first();
//         $store = DB::table('stores')
//                         ->select('id','name')
//                         ->where('id', $machine->store_id)
//                         ->first();

//         if($operationOrderDetail->out_item_id){
//             $quantity = Quantity::where('ownerable_type', 'App\Models\Store')
//                                 ->where('ownerable_id', $store->id)
//                                 ->where('item_id', $operationOrderDetail->out_item_id)
//                                 ->first();
//             if($operationOrderResult->confirmed){
//                 $quantity->decrement('quantity', $operationOrderResult->actual_output);
//                 $quantity->save();
//             }
//         }
//         if($operationOrderDetail->item_id){
//             $quantity = Quantity::where('ownerable_type', 'App\Models\Store')
//                                 ->where('ownerable_id', $store->id)
//                                 ->where('item_id', $operationOrderDetail->item_id)
//                                 ->first();
//             if($operationOrderResult->confirmed){
//                 $quantity->increment('quantity', $operationOrderResult->old_item_quantity);
//                 $quantity->save();
//             }

//         }

//         if($operationOrderDetail->is_special){
//             $item = DB::table('items')
//                         ->select('id','name')
//                         ->where('operat_ord_id', $operationOrder->id)
//                         ->where('is_special', 1)
//                         ->first();

//             if($item){
//                 $quantity = Quantity::where('ownerable_type', 'App\Models\Store')
//                                     ->where('ownerable_id', $store->id)
//                                     ->where('item_id', $item->id)
//                                     ->first();

//                 if($operationOrderResult->confirmed){
//                     $quantity->decrement('quantity', $operationOrderResult->actual_output);
//                     $quantity->save();
//                 }
//             }

//         }

//         if (!@empty($operationOrderResult->orderResultDetails)){
//             foreach ($operationOrderResult->orderResultDetails as $orderResultDetail) {

//                 $item = DB::table('items')
//                             ->select('id','name')
//                             ->where('operat_ord_id', $operationOrder->id)
//                             ->where('is_damage', 1)
//                             ->where('name', $orderResultDetail->damage_name)
//                             ->first();
//                 if($item){
//                     $quantity = Quantity::where('ownerable_type', 'App\Models\Store')
//                                         ->where('ownerable_id', $store->id)
//                                         ->where('item_id', $item->id)
//                                         ->first();
//                     $quantity->decrement('quantity', $orderResultDetail->damage_quantity);
//                     $quantity->save();

//                     $orderResultDetail->delete();
//                 }

//             }
//         }

//         $operationOrderResult->delete();
//         session()->flash('success', __('site.deleted_successfully'));
//         return redirect()->route('dashboard.operation_order_results.index');

//     }

//     public function order_result_delete_detail($id){
//         $orderResultDetail = OperationOrderResultDetail::where('id', $id)->first();
//         $operationOrderResult = OperationOrderResult::where('id', $orderResultDetail->order_results_id)->first();
//         $operationOrder = OperationOrder::where('id', $orderResultDetail->operation_order_id)->first();
//         $machine = Machines::where('id',$operationOrder->machine_id)->first();
//         $store = DB::table('stores')
//                     ->select('id','name')
//                     ->where('id', $machine->store_id)
//                     ->first();

//         $item = DB::table('items')
//                     ->select('id','name')
//                     ->where('operat_ord_id', $operationOrder->id)
//                     ->where('is_damage', 1)
//                     ->where('name', $orderResultDetail->damage_name)
//                     ->first();

//         $quantity = Quantity::where('ownerable_type', 'App\Models\Store')
//                             ->where('ownerable_id', $store->id)
//                             ->where('item_id', $item->id)
//                             ->first();
//         $quantity->decrement('quantity', $orderResultDetail->damage_quantity);
//         $quantity->save();



//         $orderResultDetail->delete();
//         session()->flash('success', __('site.deleted_successfully'));
//         return back();
//     }
//     public function getOpertOrderInfo(Request $request)
//     {
//         if (!$request->operation_order_detail_id) {
//             $html = '';
//             $html .= '<h5>برجاء اختيار امر التشغيل</h5>';

//         } else {
//             $html = '';
//             $operationOrderDetails = OperationOrderDetail::where('id', $request->operation_order_detail_id)->get();
//             foreach($operationOrderDetails as $operationOrderDetail){
//                 $operationOrders = OperationOrder::where('id', $operationOrderDetail->operation_order_id)->get();
//                 foreach ($operationOrders as $operationOrder) {
//                     $empIds = explode(',', $operationOrder->employee_id);
//                     $employeesNames = Employee::whereIn('id', $empIds)->pluck('name')->toArray();

//                     $html .= '<h5 style="text-align: center;">بيانات أمر الشغل رقم  : '.$operationOrder->id .'</h5>';
//                     $html .= '<h5 style="display:inline-block;max-width:300px;min-width:100px;margin-left: 65px;">الآلة : '.optional($operationOrder->machine)->name .'</h5>';
//                     $html .= '<h5 style="display:inline-block;max-width:300px;min-width:100px;margin-left: 65px;">مشرف : '.optional($operationOrder->supervisor)->name .'</h5>';
//                     $html .= '<h5 style="display:inline-block;max-width:350px;min-width:100px;margin-left: 65px;">الموظفين : '.implode(" , ",$employeesNames) .'</h5>';
//                     $html .= '<h5 style="display:inline-block;max-width:300px;min-width:100px;margin-left: 65px;">التاريخ : '.$operationOrder->date .'</h5>';
//                     $html .='<br>';
//                     if($operationOrder->out_operation == 0){
//                         $html .= '<h5 style="display:inline-block;max-width:350px;min-width:100px;margin-left: 65px;">اسم الخامة : '.optional($operationOrderDetail->item)->name .'</h5>';
//                         $html .= '<h5 style="display:inline-block;max-width:300px;min-width:100px;margin-left: 65px;">اسم الناتج : '.$operationOrderDetail->out_name .'</h5>';
//                     }else{
//                         $html .= '<h5 style="display:inline-block;max-width:350px;min-width:100px;margin-left: 65px;">اسم الخامة المستخدمة : '.optional($operationOrderDetail)->item_name .'</h5>';
//                         $html .= '<h5 style="display:inline-block;max-width:300px;min-width:100px;margin-left: 65px;">كمية الخامة المستخدمة : '.optional($operationOrderDetail)->old_item_quantity .'</h5>';
//                         $html .= '<h5 style="display:inline-block;max-width:300px;min-width:100px;margin-left: 65px;">الخامة الناتجة : '.$operationOrderDetail->out_item_name .'</h5>';

//                     }
//                     $html .= '<h5 style="display:inline-block;max-width:300px;min-width:100px;margin-left: 65px;">الطول : '.$operationOrderDetail->length .'</h5>';
//                     $html .= '<h5 style="display:inline-block;max-width:300px;min-width:100px;margin-left: 65px;">العرض : '.$operationOrderDetail->width .'</h5>';
//                     // $html .= '<h5 style="display:inline-block;max-width:300px;min-width:100px;margin-left: 65px;">السعر : '.$operationOrderDetail->price .'</h5>';
//                     $html .= '<h5 style="display:inline-block;max-width:300px;min-width:100px;margin-left: 65px;">الكمية : '.$operationOrderDetail->quantity .'</h5>';
//                     if(auth()->user()->can('update_operation_orders')){
//                         $html .= '<a class="btn btn-warning" style="text-decoration: none;" target="_blank" href="'.route("dashboard.operation_orders.edit", $operationOrder->id).'" ><i class="fa fa-edit"></i> Edit</a>';
//                     }
//                 }
//             }

//         }

//         return response()->json(['html' => $html]);
//     }
//     public function getOpertOrderWeight(Request $request){
//         if (isset($request->operation_order_detail_id) && isset($request->actual_output) && isset($request->thickness)) {
//             $weight = '';
//             $total_used_length = '';
//             $operationOrderDetails = OperationOrderDetail::where('id', $request->operation_order_detail_id)->get();
//             $operationOrderDetail = OperationOrderDetail::where('id', $request->operation_order_detail_id)->first();
//             $operationOrder = OperationOrder::where('id', $operationOrderDetail->operation_order_id)->first();
//             $total_used_length = $operationOrder->total_used_length;
//             // dd($operationOrder);
//             foreach ($operationOrderDetails as $operationOrderDetail) {
//                 $weight = 8*($operationOrderDetail->length)*($operationOrderDetail->width)*($request->actual_output)*($request->thickness);
//             }
//             return response()->json(['weight' => $weight, 'total_used_length'=>$total_used_length]);

//         }else if(isset($request->operation_order_detail_id)){
//             $weight = '';
//             $total_used_length = '';
//             $operationOrderDetails = OperationOrderDetail::where('id', $request->operation_order_detail_id)->first();
//             $operationOrder = OperationOrder::where('id', $operationOrderDetails->operation_order_id)->first();
//             $total_used_length = $operationOrder->total_used_length;
//             return response()->json(['weight' => $weight, 'total_used_length'=>$total_used_length]);

//         } else {
//             $weight ='';
//             $total_used_length = '';

//             return response()->json(['weight' => $weight, 'total_used_length'=>$total_used_length]);

//         }
//     }
//     public function getDamageWeight(Request $request){
//         if (isset($request->operation_order_detail_id) && isset($request->damage_length)  && isset($request->damage_quantity) && isset($request->thickness)) {
//             $weight = '';
//             $operationOrderDetails = OperationOrderDetail::where('id', $request->operation_order_detail_id)->get();
//             // dd($operationOrder);
//             foreach ($operationOrderDetails as $operationOrderDetail) {
//                 $weight = 8*($request->damage_length)*($operationOrderDetail->width)*($request->damage_quantity)*($request->thickness);
//             }
//             return response()->json(['weight' => $weight]);

//         } else {
//             $weight ='';

//             return response()->json(['weight' => $weight]);

//         }
//     }

//     public function updateConfirm(Request $request)
//     {
//         // dd($request->all());
//         // $counter = 0;
//         $requestConfirm = 0;
//         foreach($request->resource as $res){
//             $resource = OperationOrderResult::where('id', $res['itemId'])->first();
//             // dd($request->resource);
//             if ($resource) {
//                 if(array_key_exists('confirmed', $res)){
//                     if ($res['confirmed'] == 'on')
//                         $res['confirmed'] = 1;
//                     else
//                         $res['confirmed'] = 0;

//                     // $dt = new DateTime;
//                     $resource->update([
//                         'confirmed' => $res['confirmed'],
//                         'confirm_notes' => $res['confirm_notes'],
//                         'user_id' => auth()->user()->id,
//                         'confirmed_at' => date('Y-m-d h:i:s'),
//                     ]);

//                     $operationOrder = OperationOrder::where('id', $resource->operation_order_id)->first();
//                     $machine = Machines::where('id',$operationOrder->machine_id)->first();
//                     $store = DB::table('stores')
//                                 ->select('id','name')
//                                 ->where('id', $machine->store_id)
//                                 ->first();
//                     $orderDetail= OperationOrderDetail::where('id',$resource->order_details_id)->where('operation_order_id',$operationOrder->id)->first();

//                     DB::beginTransaction();

//                     //decrement old item quntity
//                     if($resource->old_item_quantity > 0){
//                         $quantity = Quantity::where('ownerable_type', 'App\Models\Store')
//                                             ->where('ownerable_id', $store->id)
//                                             ->where('item_id', $orderDetail->item_id)
//                                             ->first();


//                         $quantity->decrement('quantity', $resource->old_item_quantity);
//                         $quantity->save();

//                         $orderDetail->new_in_balance = $quantity->quantity;
//                         $orderDetail->save();
//                     }
//                     if($resource->actual_output > 0){
//                             if(isset($orderDetail->out_item_id)){
//                                 $quantity = Quantity::where('ownerable_type', 'App\Models\Store')
//                                                     ->where('ownerable_id', $store->id)
//                                                     ->where('item_id', $orderDetail->out_item_id)
//                                                     ->first();


//                                 $quantity->increment('quantity', $resource->actual_output);
//                                 $quantity->save();

//                                 $orderDetail->new_out_balance = $quantity->quantity;
//                                 $orderDetail->save();

//                             }else{
//                                 if($orderDetail->is_special == 1){    //special
//                                     $specail = Special::create([        //create in item as special
//                                         'price' => $orderDetail->price,
//                                         'name' => $orderDetail->out_name,
//                                         'length' => $orderDetail->length,
//                                         'width' => $orderDetail->width,
//                                         'weight' => $resource->weight,
//                                         'group_id' => $orderDetail->out_group_id,
//                                         'is_special' => 1,
//                                         'operat_ord_id' => $operationOrder->id,
//                                     ]);
//                                     $specail->code = $specail->id;
//                                     $specail->save();

//                                     $quantity = Quantity::create([                  //create in quantity
//                                         'ownerable_type'    => 'App\Models\Store',
//                                         'ownerable_id' => $store->id,
//                                         'item_id' => $specail->id,
//                                         'quantity' => $resource->actual_output
//                                     ]);
//                                 }
//                             }


//                     }

//                     $orderResultDetails= OperationOrderResultDetail::where('order_results_id',$resource->id)->get();

//                     foreach ($orderResultDetails as $orderResultDetail) {
//                         if($orderResultDetail->damage_quantity > 0){
//                             if(isset($orderResultDetail->old_damage_id)){
//                                 $quantity = Quantity::where('ownerable_type', 'App\Models\Store')
//                                                     ->where('ownerable_id', $store->id)
//                                                     ->where('item_id', $orderResultDetail->old_damage_id)
//                                                     ->first();
//                                 $quantity->increment('quantity', $orderResultDetail->damage_quantity);
//                                 $quantity->save();
//                             }else{
//                                 $damageGroup = DB::table('groups')->where('name','Damage')->first();

//                                 $damage = Damage::create([        //create in item as damage
//                                     'price' => $orderResultDetail->damage_price,
//                                     'name' => $orderResultDetail->damage_name,
//                                     'is_damage' => 1,
//                                     'length' => $orderResultDetail->damage_length,
//                                     'width' => $orderResultDetail->damage_width,
//                                     'weight' => $orderResultDetail->damage_weight,
//                                     'damage_type' => $orderResultDetail->damage_type,
//                                     'group_id' => $damageGroup->id,
//                                     'operat_ord_id' => $operationOrder->id,
//                                 ]);

//                                 $damage->code = $damage->id;
//                                 $damage->save();

//                                 $quantity = Quantity::create([                  //create in quantity
//                                     'ownerable_type'    => 'App\Models\Store',
//                                     'ownerable_id' => $store->id,
//                                     'item_id' => $damage->id,
//                                     'quantity' => $orderResultDetail->damage_quantity
//                                 ]);

//                                 //decrement the damage quantity from old item quantity
//                                 $oldItemQuantity = Quantity::where('ownerable_type', 'App\Models\Store')
//                                                                 ->where('ownerable_id', $store->id)
//                                                                 ->where('item_id', $orderDetail->item_id)
//                                                                 ->first();
//                                 $oldItemQuantity->decrement('quantity', $orderResultDetail->damage_weight);
//                                 $oldItemQuantity->save();

//                             }

//                         }
//                     }

//                     DB::commit();

//                 }
//                 if(array_key_exists('confirm_notes',$res)){
//                     // $dt = new DateTime;
//                     $resource->update([
//                         'confirm_notes' => $res['confirm_notes'],
//                     ]);
//                 }


//             }

//             // $counter++;
//         }
//         session()->flash('success', __('site.confirmed_successfully'));
//         return redirect()->route('dashboard.operation_order_results.index');
//     }

//     public function updateConfirmOut(Request $request)
//     {
//         // dd($request->all());
//         // $counter = 0;
//         $requestConfirm = 0;
//         foreach($request->resource as $res){
//             $resource = OperationOrderResult::where('id', $res['itemId'])->first();
//             // dd($request->resource);
//             if ($resource) {
//                 if(array_key_exists('confirmed', $res)){
//                     if ($res['confirmed'] == 'on')
//                         $res['confirmed'] = 1;
//                     else
//                         $res['confirmed'] = 0;

//                     // $dt = new DateTime;


//                     $operationOrder = OperationOrder::where('id', $resource->operation_order_id)->first();

//                     DB::beginTransaction();

//                     $resource->update([
//                         'confirmed' => $res['confirmed'],
//                         'confirm_notes' => $res['confirm_notes'],
//                         'user_id' => auth()->user()->id,
//                         'confirmed_at' => date('Y-m-d h:i:s'),
//                     ]);

//                     $operationOrder->update([
//                         'out_confirmed' => $res['confirmed'],
//                         'confirm_notes' => $res['confirm_notes'],
//                         'confirm_user_id' => auth()->user()->id,
//                         'confirmed_at' => date('Y-m-d h:i:s'),
//                     ]);

//                     DB::commit();

//                 }
//                 if(array_key_exists('confirm_notes',$res)){
//                     // $dt = new DateTime;
//                     $resource->update([
//                         'confirm_notes' => $res['confirm_notes'],
//                     ]);
//                     $operationOrder->update([
//                         'confirm_notes' => $res['confirm_notes'],
//                     ]);
//                 }


//             }

//             // $counter++;
//         }
//         session()->flash('success', __('site.confirmed_successfully'));
//         return redirect()->route('dashboard.operation_order_results.index_out');
//     }

//     public function push_notification($message)
//     {
//         $options = array(
//             'cluster' => 'eu',
//             'useTLS' => true
//         );
//         $pusher = new Pusher(
//             'e75d58425f4b10f93cfb',
//             '49edd2fdb43527c84354',
//             '417914',
//             $options
//         );
//         $data['message'] = $message;
//         $pusher->trigger('my-channel', 'my-event', $data);
//         return true;
//     }
// }
