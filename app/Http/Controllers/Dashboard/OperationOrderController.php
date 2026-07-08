<?php
//------------------------------------------------------------------------------------------------- ~Mohamed Maher~ -------------------------------------------------------------------------------------------------------------------------//


namespace App\Http\Controllers\Dashboard;

use App\Department;
use App\Admin;
use App\Reposite;
use App\SupplieTypes;
use App\Supplies;
use App\OperationOrder;
use App\OperationOrderDetail;
use App\OperationOrderResult;
use App\Machines;
use App\MachineItem;
use App\MachineGroup;
use App\MachineTypes;
use App\MachineSupplie;
use App\Item;
use App\Quantity;
use App\Group;
use Auth;
use App\Employee;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables as FacadesDataTables;
use Storage;
use Pusher\Pusher;
use Illuminate\Support\Arr;

class OperationOrderController extends Controller
{
    public function __construct()
    {
        $this->middleware(['ability:admin,reade_operation_orders'])->only('index');
        $this->middleware(['ability:admin,create_operation_orders'])->only('create');
        $this->middleware(['ability:admin,update_operation_orders'])->only('edit');
        $this->middleware(['ability:admin,delete_operation_orders'])->only('destroy');
    }


    public function getData(Request $request)
    {
        $query = OperationOrder::query();

        // if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('مسئول فرع و مصنع' || auth()->user()->hasRole('مسئول مصنع') || auth()->user()->hasRole('مسئول مخزن و مصنع'))){

        //  if (auth()->user()->hasRole('admin') || auth()->user()->hasRole('branch_factor_respons') || auth()->user()->hasRole('store_factor_response') || auth()->user()->hasRole('factor_response')) {
        //     if (isset($request['type']) && !empty($request['type'])) {
        //         $query->where('out_operation', 0)->whereHas('operationOrderDetails', function ($q) {
        //             $q->whereDoesntHave('operationOrderResults');
        //         })->get();
        //     } else {
        //         $query->where('out_operation', 0)->get();
        //     }
        // } else {
        //     $query = OperationOrder::toUser();
        //     if (isset($request['type']) && !empty($request['type'])) {
        //         $query->where('out_operation', 0)->whereHas('operationOrderDetails', function ($q) {
        //             $q->whereDoesntHave('operationOrderResults');
        //         })->get();
        //     } else {
        //         $query->where('out_operation', 0);
        //     }
        // }
        
        
        if (isset($request['type']) && !empty($request['type'])) {
            $query->where('out_operation', 0)->whereHas('operationOrderDetails', function ($q) {
                $q->whereDoesntHave('operationOrderResults');
            });
        }

        if (auth()->user()->hasRole('admin') || auth()->user()->hasRole('admin2') || auth()->user()->hasRole('safe_factor_response') || auth()->user()->hasRole('branch_factor_respons') || auth()->user()->hasRole('store_factor_response') || auth()->user()->hasRole('factor_response')) {
            $query->where('out_operation', 0)->get();
        } else if (auth()->user()->hasRole('branch_factor_respons') || auth()->user()->hasRole('safe_factor_response') || auth()->user()->hasRole('store_factor_response') || auth()->user()->hasRole('factor_response')) {
            $query->where('out_operation', 0)->where('supervisor_process', auth()->user()->id)->get();
        } else if (auth()->user()->hasRole('machine_response')) {
            $query->where('out_operation', 0)->where('user_id', auth()->user()->id)->get();
        } else {
            $query = OperationOrder::toUser();
            $query->where('out_operation', 0);
        }

        // if (auth()->user()->hasRole('admin') || auth()->user()->hasRole('branch_factor_respons') || auth()->user()->hasRole('store_factor_response') || auth()->user()->hasRole('factor_response')) {
        //     $query->where('out_operation', 0)->get();
        // } else {
        //     $query = OperationOrder::toUser();
        //     $query->where('out_operation', 0);
        // }

        $i = 0;
        return FacadesDataTables::eloquent($query->with(['machine', 'employee', 'user', 'supervisor', 'item', 'store', 'group', 'oberationSupply'])->latest())
            ->addColumn('action', function (OperationOrder $operationOrder) use (&$i) {
                $i++;
                $type = "action";
                return view("dashboard.operation_orders.action", compact("operationOrder", "type", "i"));
            })
            ->editColumn('machine_id', function (OperationOrder $operationOrder) {
                return optional($operationOrder->machine)->name;
            })
            ->editColumn('employee_id', function (OperationOrder $operationOrder) {
                $empIds = explode(',', $operationOrder->employee_id);
                $employeesNames = Employee::whereIn('id', $empIds)->pluck('name')->toArray();
                return $employeesNames;
            })
            ->editColumn('user_id', function (OperationOrder $operationOrder) {
                $userIds = explode(',', $operationOrder->user_id);
                $usersNames = User::whereIn('id', $userIds)->pluck('name')->toArray();
                return $usersNames;
            })

            ->editColumn('supervisor_store', function (OperationOrder $operationOrder) {
                $userIds = explode(',', $operationOrder->supervisor_store);
                $usersNames = User::whereIn('id', $userIds)->pluck('name')->toArray();
                return $usersNames;
            })

            ->editColumn('supervisor_process', function (OperationOrder $operationOrder) {
                $userIds = explode(',', $operationOrder->supervisor_process);
                $usersNames = User::whereIn('id', $userIds)->pluck('name')->toArray();
                return $usersNames;
            })

            // ->editColumn('operation_suplies_id',function(OperationOrder $operationOrder){
            //     $operationSupliesIds = explode(',', $operationOrder->operation_suplies_id);
            //     $operationSupliesNames = Supplies::whereIn('id', $operationSupliesIds)->pluck('name')->toArray();
            //     return $operationSupliesNames;
            // })
            // ->editColumn('item_id',function(OperationOrder $operationOrder){
            //     return optional($operationOrder->item)->name;
            // })
            // ->editColumn('out_group_id',function(OperationOrder $operationOrder){
            //     return optional($operationOrder->group)->name;
            // })
            ->editColumn('related_operat_ord_id', function (OperationOrder $operationOrder) {
                if (isset($operationOrder->related_operat_ord_id)) {
                    return '<a style="text-decoration: none;" target="_blank" href="' . route("dashboard.operation_orders.edit", $operationOrder->related_operat_ord_id) . '" >' . $operationOrder->related_operat_ord_id . '</a>';
                } else {
                    return '';
                }
            })
            ->rawColumns(['action', 'related_operat_ord_id'])
            ->toJson();
    }

    public function getDataOut(Request $request)
    {
        $query = OperationOrder::query();

        // if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('مسئول فرع و مصنع' || auth()->user()->hasRole('مسئول مصنع') || auth()->user()->hasRole('مسئول مخزن و مصنع'))){

        // if (auth()->user()->hasRole('admin') || auth()->user()->hasRole('branch_factor_respons') || auth()->user()->hasRole('store_factor_response') || auth()->user()->hasRole('factor_response')) {
        //     if (isset($request['type']) && !empty($request['type'])) {
        //         $query->where('out_operation', 1)->where('date','>=','2023-12-01')->whereHas('operationOrderDetails', function ($q) {
        //             $q->whereDoesntHave('operationOrderResults');
        //         })->get();
        //     } else {
        //         $query->where('out_operation', 1)->get();
        //     }
        // } else {
        //     $query = OperationOrder::toUser();
        //     if (isset($request['type']) && !empty($request['type'])) {
        //         $query->where('out_operation', 1)->where('date','>=','2023-12-01')->whereHas('operationOrderDetails', function ($q) {
        //             $q->whereDoesntHave('operationOrderResults');
        //         })->get();
        //     } else {
        //         $query->where('out_operation', 1);
        //     }
        // }
        
        // if (isset($request['type']) && !empty($request['type'])) {
        //     $query->where('out_operation', 1)->where('date', '>', '2023-12-02')->whereHas('operationOrderDetails', function ($q) {
        //         $q->whereDoesntHave('operationOrderResults');
        //     });
        // }
        
        if (isset($request['type']) && !empty($request['type'])) {
            if($request['type'] == 'complete') {
                $query->where('is_complete', 0);
            } else {
                $query->where('out_operation', 1)->where('is_complete', 1)->where('date', '>', '2023-12-02')->whereHas('operationOrderDetails', function ($q) {
                    $q->whereDoesntHave('operationOrderResults');
                });
            }
        }
        //
        
        if (auth()->user()->hasRole('admin') || auth()->user()->hasRole('admin2') || auth()->user()->hasRole('safe_factor_response') || auth()->user()->hasRole('branch_factor_respons') || auth()->user()->hasRole('store_factor_response') || auth()->user()->hasRole('factor_response')) {
            $query->where('out_operation', 1)->where('date', '>', '2023-12-02')->get();
        } else if (auth()->user()->hasRole('branch_factor_respons') || auth()->user()->hasRole('safe_factor_response') || auth()->user()->hasRole('store_factor_response') || auth()->user()->hasRole('factor_response')) {
            $query->where('out_operation', 1)->where('date', '>', '2023-12-02')->where('supervisor_process', auth()->user()->id)->get();
        } else if (auth()->user()->hasRole('machine_response')) {
            $query->where('out_operation', 1)->where('date', '>', '2023-12-02')->where('user_id', auth()->user()->id)->get();
        } else {
            $query = OperationOrder::toUser();
            $query->where('out_operation', 1)->where('date', '>', '2023-12-02');
        }

        // if (auth()->user()->hasRole('admin') || auth()->user()->hasRole('branch_factor_respons') || auth()->user()->hasRole('store_factor_response') || auth()->user()->hasRole('factor_response')) {
        //     $query->where('out_operation', 1)->get();
        // } else {
        //     $query = OperationOrder::toUser();
        //     $query->where('out_operation', 1);
        // }

        return FacadesDataTables::eloquent($query->with(['machine', 'employee', 'user', 'supervisor', 'item', 'store', 'group', 'oberationSupply'])->latest())
            ->addColumn('action', function (OperationOrder $operationOrder) {
                $type = "action";
                return view("dashboard.operation_orders.action", compact("operationOrder", "type"));
            })
            ->addColumn('confirm_notes', function (OperationOrder $operationOrder) {
                $type = "confirm_notes";
                return view("dashboard.operation_orders.action", compact("operationOrder", "type"));
            })
            ->editColumn('machine_id', function (OperationOrder $operationOrder) {
                return optional($operationOrder->machine)->name;
            })
            ->editColumn('employee_id', function (OperationOrder $operationOrder) {
                $empIds = explode(',', $operationOrder->employee_id);
                $employeesNames = Employee::whereIn('id', $empIds)->pluck('name')->toArray();
                return $employeesNames;
            })
            ->editColumn('user_id', function (OperationOrder $operationOrder) {
                $userIds = explode(',', $operationOrder->user_id);
                $usersNames = User::whereIn('id', $userIds)->pluck('name')->toArray();
                return $usersNames;
            })
            ->editColumn('supervisor_store', function (OperationOrder $operationOrder) {
                $userIds = explode(',', $operationOrder->supervisor_store);
                $usersNames = User::whereIn('id', $userIds)->pluck('name')->toArray();
                return $usersNames;
            })

            ->editColumn('supervisor_process', function (OperationOrder $operationOrder) {
                $userIds = explode(',', $operationOrder->supervisor_process);
                $usersNames = User::whereIn('id', $userIds)->pluck('name')->toArray();
                return $usersNames;
            })

            ->editColumn('confirm_user_id', function (OperationOrder $operationOrder) {
                if ($operationOrder->confirm_user_id) {
                    $confirmUser = DB::table('users')->select('name')->where('id', $operationOrder->confirm_user_id)->first()->name;

                    return ' ' . $confirmUser . '<br>' . $operationOrder->confirmed_at;
                }
                return '';
            })
            ->editColumn('operation_suplies_id', function (OperationOrder $operationOrder) {
                $operationSupliesIds = explode(',', $operationOrder->operation_suplies_id);
                $operationSupliesNames = Supplies::whereIn('id', $operationSupliesIds)->pluck('name')->toArray();
                return $operationSupliesNames;
            })
            ->editColumn('supervisor_id', function (OperationOrder $operationOrder) {
                return optional($operationOrder->supervisor)->name;
            })
            ->editColumn('item_id', function (OperationOrder $operationOrder) {
                return optional($operationOrder->item)->name;
            })
            ->editColumn('out_group_id', function (OperationOrder $operationOrder) {
                return optional($operationOrder->group)->name;
            })
            ->editColumn('related_operat_ord_id', function (OperationOrder $operationOrder) {
                if (isset($operationOrder->related_operat_ord_id)) {
                    return '<a style="text-decoration: none;" target="_blank" href="' . route("dashboard.operation_orders.edit", $operationOrder->related_operat_ord_id) . '" >' . $operationOrder->related_operat_ord_id . '</a>';
                } else {
                    return '';
                }
            })
            ->rawColumns(['action', 'confirm_notes', 'confirm_user_id', 'related_operat_ord_id'])
            ->toJson();
    }

    public function index(Request $request)
    {
        $query = OperationOrder::query();
        $operationOrder = $query->where('out_operation', 0)->get();
        if (isset($request['type']) && !empty($request['type'])) {
            return view('dashboard.operation_orders.index2', compact('operationOrder'));
        }
        return view('dashboard.operation_orders.index', compact('operationOrder'));
    }

    public function indexOut(Request $request)
    {
        $query = OperationOrder::query();

        $operationOrder = $query->where('out_operation', 1)->get();
        // if (isset($request['type']) && !empty($request['type'])) {
        //     return view('dashboard.operation_orders.index_out2', compact('operationOrder'));
        // }
        
        if (isset($request['type']) && !empty($request['type'])) {
            if($request['type'] == 'complete') {
                return view('dashboard.operation_orders.index_out3', compact('operationOrder'));
            }
            else {
                return view('dashboard.operation_orders.index_out2', compact('operationOrder'));
            }
        }
        //
        return view('dashboard.operation_orders.index_out', compact('operationOrder'));
    }

    public function summary()
    {
        $baseQuery = OperationOrder::whereHas('tracks');

        $allOrders = (clone $baseQuery)->with('tracks')->get();

        $stats = [
            'totalOrders' => $allOrders->count(),
            'completedOrders' => $allOrders
                ->filter(function ($o) {
                    return $o->tracks->where('status', 'pending')->isEmpty() && $o->tracks->where('status', 'approved')->isNotEmpty() && $o->tracks->where('status', 'rejected')->isEmpty();
                })
                ->count(),
            'rejectedOrders' => $allOrders
                ->filter(fn($o) => $o->tracks->where('status', 'rejected')->isNotEmpty())
                ->count(),
        ];

        $stats['pendingOrders'] = $stats['totalOrders'] - $stats['completedOrders'] - $stats['rejectedOrders'];

        $operationOrders = (clone $baseQuery)->with(['machine', 'employee', 'item', 'tracks.user', 'operationOrderDetails'])->orderBy('machine_id')->orderByDesc('id')->get();

        return view('dashboard.operation_orders.summary', compact('operationOrders', 'stats'));
    }

    public function create() {
        $users = User::where('role_id', 7)->get();
        $admins = User::where('role_id', 8)->get();
        $supervisor_store = User::where('role_id',9)->get();
        $machineTypes = MachineTypes::latest()->get();

        return view('dashboard.operation_orders.create', compact('users', 'admins', 'supervisor_store', 'machineTypes'));
    }

    public function store(Request $request) {
        $request->validate([
            'date'               => 'required',
            'machine_type_id'    => 'required',
            'machine_id'         => 'required',
            'user_id'            => 'required',
            'machine_type_id'    => 'required',
            'supervisor_store'   => 'required',
            'supervisor_process' => 'required',
            'notes'              => 'nullable',
        ]);

        if (empty($request->item_id)) {
            session()->flash('error', 'الرجاء تحديد البنود الفرعية بأمر التشغيل الدخلى');
            return redirect()->back()->withInput();
        }

        $machine = DB::table('machines')->where('id', $request->machine_id)->select(['id', 'store_id'])->first();
        $store = DB::table('stores')->where('id', $machine->store_id)->select(['id', 'name'])->first();

        $requestData = $request->only([
            'date', 'machine_type_id', 'machine_id', 'user_id', 'supervisor_store', 'supervisor_process', 'notes'
        ]);

        if(!empty($request['user_id'])) {
            $users = $request->input('user_id');
            $requestData['user_id'] = implode(',', $users);
        }

        if(!empty($request['supervisor_store'])) {
            $supervisorStore = $request->input('supervisor_store');
            $requestData['supervisor_store'] = implode(',', $supervisorStore);
        }

        if(!empty($request['supervisor_process'])) {
            $supervisorProcess = $request->input('supervisor_process');
            $requestData['supervisor_process'] = implode(',', $supervisorProcess);
        }
        
        $requestData['store_id'] = $machine->store_id;
        $requestData['created_by'] = auth()->user()->id;

        $operationOrder = OperationOrder::create($requestData);

        $counter = 0;
        foreach ($request->item_id as $item) {
            $dbInQuantity = DB::table('quantities')->where('ownerable_type', 'App\Models\Store')
                                                    ->where('ownerable_id', $store->id)
                                                    ->where('item_id', $request->item_id[$counter])
                                                    ->first();

            $orderDetailCreate = OperationOrderDetail::create([
                'operation_order_id' => $operationOrder->id,
                'group_id' => $request->group_id[$counter],
                'item_id' => $request->item_id[$counter],
                'old_in_balance' => !empty($dbInQuantity) ? $dbInQuantity->quantity : NULL,
                'old_item_quantity' => $request->old_item_quantity[$counter],
                'price' => $request->price[$counter],
                'length' => $request->length[$counter],
                'width' => $request->width[$counter],
                'quantity' => $request->quantity[$counter],
                'old_item_supp_quantity' => $request->old_item_supp_quantity[$counter],
                'supplie_quantity_used' => $request->old_item_supp_quantity[$counter] * $request->length[$counter],
                'out_group_id' => $request->out_group_id[$counter],
                'out_name' => $request->out_name[$counter],
            ]);

            if (!empty($request->operation_suplies_id[$counter])) {
                $operationSuplies = $request->input('operation_suplies_id');
                $impSupliesIds  = implode(',', $operationSuplies[$counter]);
                $expSupliesIds  = explode(',', $impSupliesIds);
                $suppliesIds = DB::table('machine_supplies')->whereIn('id', $expSupliesIds)->pluck('supplie_id')->toArray();
                $suppliesused = DB::table('machine_supplies')->whereIn('id', $expSupliesIds)->pluck('used')->toArray();
                
                $orderDetailCreate->operation_suplies_id = implode(',', $suppliesIds);
                $orderDetailCreate->supplie_quantity_pre_used = implode(',', $suppliesused);
                $orderDetailCreate->save();
            }

            if (!empty($request->out_item_id[$counter])) {
                $dbOutQuantity = DB::table('quantities')->where('ownerable_type', 'App\Models\Store')
                                                        ->where('ownerable_id', $store->id)
                                                        ->where('item_id', $request->out_item_id[$counter])
                                                        ->first();

                $orderDetailCreate->out_item_id = $request->out_item_id[$counter];
                $orderDetailCreate->old_out_balance = !empty($dbOutQuantity) ? $dbOutQuantity->quantity : NULL;
                $orderDetailCreate->save();
            }

            $counter++;
        }

        for ($index = 0; $index < count($users); $index++) {
            $this->push_notification(['user_id' => $users[$index], 'url' => url('operation_orders')]);
        }

        $machineBranch = DB::table('branches')->where('name', $store->name)->first();
        $rolesIds = DB::table('roles')->whereIn('name', ['factor_response', 'branch_factor_respons', 'safe_factor_response'])->pluck('id')->toArray();
        $usersRespons = DB::table('users')->where('branch_id', $machineBranch->id)->whereIn('role_id', $rolesIds)->pluck('id')->toArray();

        for ($index = 0; $index < count($usersRespons); $index++) {
            $this->push_notification(['user_id' => $usersRespons[$index], 'url' => url('operation_orders')]);
        }
    
        $operationOrder->tracks()->createMany([
            [
                'step_name' => 'warehouse_supervisor',
            ],
            [
                'step_name' => 'machine_manager',
            ],
            [
                'step_name' => 'production_manager',
            ],
            [
                'step_name' => 'store_manager',
            ],
        ]);

        session()->flash('success', __('site.added_successfully'));
        return redirect()->route('dashboard.operation_orders.index');
    }
    
    public function edit($operationOrder) {

        // return $operationOrder;

        //new

            $operationOrderDetails = DB::table('operation_order_details')
        ->where('operation_order_id', $operationOrder)
        ->first();

         $selectedSuppliesIds = !empty($operationOrderDetails->operation_suplies_id) 
        ? explode(',', $operationOrderDetails->operation_suplies_id) 
        : [];


        //end
     
        $operation_OrderDetails = OperationOrderDetail::where('operation_order_id', $operationOrder)->get();
          
        $operationOrder = OperationOrder::find($operationOrder);
        
        

        // dd($activeStatus);
     

        $users = User::where('role_id', 7)->get();
        $admins = User::where('role_id', 8)->get();
        $supervisor_store = User::where('role_id',9)->get();
        $machineTypes = MachineTypes::latest()->get();
        $relatedOperationOrders = OperationOrder::latest()->get();

        return view('dashboard.operation_orders.edit', compact('operationOrder', 'users', 'admins', 'supervisor_store', 'machineTypes', 'relatedOperationOrders','operation_OrderDetails','selectedSuppliesIds'));
    }

    public function update(Request $request, $id) {
        $request->validate([
            'date'                     => 'required',
            'machine_type_id'          => 'required',
            'machine_id'               => 'nullable',
            'user_id'                  => 'required',
            'supervisor_store'         => 'required',
            'supervisor_process'       => 'required',
            'notes'                    => 'nullable',
            'quantity.*'               => 'required|numeric|min:0',
            'old_item_quantity.*'      => 'required|numeric|min:0',
            'supplie_quantity_used.*'  => 'required|numeric|min:0',
            'old_item_supp_quantity.*' => 'required|numeric|min:0',
            'out_name.*'               => 'required|string|max:255',
            'price.*'                  => 'required|numeric|min:0',
            'width.*'                  => 'required|numeric|min:0',
            'length.*'                 => 'required|numeric|min:0',
        ]);
    
        $operationOrder = OperationOrder::find($id);
       
        $requestData = $request->only([
            'date', 'machine_type_id', 'machine_id', 'user_id', 'supervisor_store', 'supervisor_process', 'notes'
        ]);
    
        if (!empty($request['user_id'])) {
            $users = $request->input('user_id');
            $requestData['user_id'] = implode(',', $users);
        }
    
        if (!empty($request['supervisor_store'])) {
            $supervisorStore = $request->input('supervisor_store');
            $requestData['supervisor_store'] = implode(',', $supervisorStore);
        }
    
        if (!empty($request['supervisor_process'])) {
            $supervisorProcess = $request->input('supervisor_process');
            $requestData['supervisor_process'] = implode(',', $supervisorProcess);
        }
    

        if (!empty($request->input('machine_id'))) {
            $requestData['machine_id'] = $request->input('machine_id');
        } else {
            $requestData['machine_id'] = $operationOrder->machine_id;
        }
    
        $machine = DB::table('machines')->where('id', $requestData['machine_id'])->select(['id', 'store_id'])->first();
        $store = DB::table('stores')->where('id', $machine->store_id)->select(['id', 'name'])->first();
    
        $operationOrder->update($requestData);
 
        $counter = 0;
        if (!empty($request->item_id)) {
            foreach ($request->item_id as $item) {
            
                $dbInQuantity = DB::table('quantities')->where('ownerable_type', 'App\Models\Store')
                                                      ->where('ownerable_id', $store->id)
                                                      ->where('item_id', $request->item_id[$counter])
                                                      ->first();
    
                if (!empty($dbInQuantity)) {
                    if ($request->old_item_quantity[$counter] > $dbInQuantity->quantity) {
                        session()->flash('error', 'لا يمكن ان تكون كمية الخامة المستخدمة اكبر من المتاحة للألة');
                        return redirect()->back();
                    }
                } else {
                    session()->flash('error', 'لا يوجد كمية في هذا المخزن');
                    return redirect()->back();
                }
           
                if (empty($request->operation_order_detail_id[$counter])) {
                    $orderDetailCreate = OperationOrderDetail::create([
                        'operation_order_id' => $operationOrder->id,
                        'group_id' => $request->group_id[$counter],
                        'item_id' => $request->item_id[$counter],
                        'old_in_balance' => !empty($dbInQuantity) ? $dbInQuantity->quantity : NULL,
                        'old_item_quantity' => $request->old_item_quantity[$counter],
                        'price' => $request->price[$counter],
                        'length' => $request->length[$counter],
                        'width' => $request->width[$counter],
                        'quantity' => $request->quantity[$counter],
                        'old_item_supp_quantity' => $request->old_item_supp_quantity[$counter],
                        'supplie_quantity_used' => $request->old_item_supp_quantity[$counter] * $request->length[$counter],
                        'out_group_id' => $request->out_group_id[$counter],
                        'out_name' => $request->out_name[$counter],
                    ]);
    
                    if (!empty($request->out_item_id[$counter])) {
                        $dbOutQuantity = DB::table('quantities')->where('ownerable_type', 'App\Models\Store')
                                                                ->where('ownerable_id', $store->id)
                                                                ->where('item_id', $request->out_item_id[$counter])
                                                                ->first();
    
                        $orderDetailCreate->out_item_id = $request->out_item_id[$counter];
                        $orderDetailCreate->old_out_balance = !empty($dbOutQuantity) ? $dbOutQuantity->quantity : NULL;
                        $orderDetailCreate->save();
                    }

                    if (!empty($request->operation_suplies_id[$counter])) {
                        $operationSuplies = $request->input('operation_suplies_id');
                        $impSupliesIds  = implode(',', $operationSuplies[$counter]);
                        $expSupliesIds  = explode(',', $impSupliesIds);
                        $suppliesIds = DB::table('machine_supplies')->whereIn('id', $expSupliesIds)->pluck('supplie_id')->toArray();
                        $suppliesused = DB::table('machine_supplies')->whereIn('id', $expSupliesIds)->pluck('used')->toArray();
                        $suppliesIds = implode(',', $suppliesIds);
                        $suppliesused = implode(',', $suppliesused);
                        $orderDetailCreate->update([
                            'operation_suplies_id'      => $suppliesIds,
                            'supplie_quantity_pre_used' => $suppliesused,
                        ]);
                    }
                }
                else if (!empty($request->operation_order_detail_id[$counter])) {
                    OperationOrderDetail::where('id', $request->operation_order_detail_id[$counter])->update([
                        'quantity' => $request->quantity[$counter],
                        'old_item_quantity' => $request->old_item_quantity[$counter],
                        'old_item_supp_quantity' => $request->old_item_supp_quantity[$counter],
                        'supplie_quantity_used' => $request->old_item_supp_quantity[$counter] * $request->length[$counter],
                        'price' => $request->price[$counter],
                        'length' => $request->length[$counter],
                        'width' => $request->width[$counter],
                    ]);

                    if (!empty($request->operation_suplies_id[$counter])) {
                        $operationSuplies = $request->input('operation_suplies_id');
                        $impSupliesIds  = implode(',', $operationSuplies[$counter]);
                        $expSupliesIds  = explode(',', $impSupliesIds);
                        $suppliesIds = DB::table('machine_supplies')->whereIn('id', $expSupliesIds)->pluck('supplie_id')->toArray();
                        $suppliesused = DB::table('machine_supplies')->whereIn('id', $expSupliesIds)->pluck('used')->toArray();
                        $suppliesIds = implode(',', $suppliesIds);
                        $suppliesused = implode(',', $suppliesused);
                        OperationOrderDetail::where('id', $request->operation_order_detail_id[$counter])->update([
                            'operation_suplies_id'      => $suppliesIds,
                            'supplie_quantity_pre_used' => $suppliesused,
                        ]);
                    }
                }
                $counter++;
            }
        }
    
        $selectedItems = $request->input('selected_items');
        if (!empty($selectedItems)) {
            DB::table('operation_order_details')->whereIn('id', $selectedItems)->delete();
        }

        for ($index = 0; $index < count($users); $index++) {
            $this->push_notification(['user_id' => $users[$index], 'url' => url('operation_orders')]);
        }
     
        $machineBranch = DB::table('branches')->where('name', $store->name)->first();
        $rolesIds = DB::table('roles')->whereIn('name', ['factor_response', 'branch_factor_respons', 'safe_factor_response'])->pluck('id')->toArray();
        $usersRespons = DB::table('users')->where('branch_id', $machineBranch->id)->whereIn('role_id', $rolesIds)->pluck('id')->toArray();
    
        for ($index = 0; $index < count($usersRespons); $index++) {
            $this->push_notification(['user_id' => $usersRespons[$index], 'url' => url('operation_orders')]);
        }
       
        session()->flash('success', __('site.updated_successfully'));
        return redirect()->route('dashboard.operation_orders.index');
    }

    public function createOut()
    {
        if (auth()->user()->hasRole('admin') || auth()->user()->hasRole('safe_factor_response') || auth()->user()->hasRole('branch_factor_respons') || auth()->user()->hasRole('store_factor_response') || auth()->user()->hasRole('factor_response')) {
            $operationOrders = OperationOrder::where('out_operation', 1)->latest()->get();
        } else {
            $operationOrders = OperationOrder::toUser()->where('out_operation', 1)->latest()->get();
        }

        $machines = Machines::all();
        $employees = Employee::where('active', 1)->where('branch_id', 6)->latest()->get();
        // $users = User::where('user_type', 'factory_user')->latest()->get();

        $users = User::where('role_id', 7)->get();
        // $supervisor_store = User::whereIn('role_id', [2, 9])->get();
        $supervisor_store = User::where('role_id',9)->get();
        $admins = User::where('role_id', 8)->get();
        $clients = DB::table('clients')->latest()->get();
        
        $supplieTypes = SupplieTypes::all();
        // $supplies = Supplies::all();
        $groups = Group::latest()->get();
        $machineTypes = MachineTypes::latest()->get();

        return view('dashboard.operation_orders.create_out', compact('machines', 'users', 'operationOrders', 'machineTypes', 'employees', 'groups', 'supplieTypes', 'admins', 'supervisor_store','clients'));
    }

    public function storeOut(Request $request)
    {
        // return $request->all();
        //dd($request->is_special);

        $request->validate([
            'machine_id' => 'required',
            // 'supervisor_id' => 'required',
            // 'employee_id' => 'required',
            // 'user_id' => 'required',
            'date' => 'required',
            // 'total_used_length' => 'required',
            'operation_suplies_id' => 'required',
            'quantity' => 'required',
            'length' => 'required',
        ]);

        $request_data = $request->except(['employee_id', 'operation_suplies_id', 'item_name', 'out_item_name', 'old_item_quantity', 'out_name', 'length', 'width', 'quantity']);
        $request_data['out_operation'] = 1;

        /**
         * There are array of employee
         */
        if (isset($request['employee_id'])) {
            $employees = $request->input('employee_id');
            $request_data['employee_id'] = implode(',', $employees);
        }
        
        if(isset($request['store_employees'])) {
            $employees = $request->input('store_employees');
            $request_data['store_employees'] = implode(',', $employees);
        }


        $request_data['store_id'] = Machines::where('id', $request->machine_id)->first()->store_id;

        $operationOrder = OperationOrder::create($request_data);
        // $operationOrder->update(['created_by' => auth()->user()->id]);
        
        $machine = Machines::where('id', $request->machine_id)->first();
        $store = DB::table('stores')
            ->select('id', 'name')
            ->where('id', $machine->store_id)
            ->first();
        $counter = 0;
        foreach ($request->quantity as $item) {
            if (isset($request->quantity[$counter])) {
                $orderDetailCreate = OperationOrderDetail::create([
                    'operation_order_id' => $operationOrder->id,
                    'length' => $request->length[$counter],
                    'width' => $request->width[$counter],
                    'old_item_quantity' => $request->old_item_quantity[$counter],
                    'item_name' => $request->item_name[$counter],
                    'quantity' => $request->quantity[$counter],
                    'out_item_name' => $request->out_item_name[$counter],
                    'supplie_quantity_used' => $request->quantity[$counter] * $request->length[$counter]
                    // 'out_name' => $request->out_name[$counter],
                ]);

                if ($request->operation_suplies_id[$counter]) {
                    $operationSuplies = $request->input('operation_suplies_id');
                    $impSupliesIds  = implode(',', $operationSuplies[$counter]);
                    $expSupliesIds  = explode(',', $impSupliesIds);

                    $suppliesIds = MachineSupplie::whereIn('id', $expSupliesIds)->pluck('supplie_id')->toArray();
                    $orderDetailCreate->operation_suplies_id = implode(',', $suppliesIds);
                    $suppliesused = MachineSupplie::whereIn('id', $expSupliesIds)->pluck('used')->toArray();
                    $orderDetailCreate->supplie_quantity_pre_used = implode(',', $suppliesused);
                    $orderDetailCreate->save();
                }
                if ($orderDetailCreate->operation_suplies_id) {

                    $operationSuplies = $request->input('operation_suplies_id');
                    $impSupliesIds  = implode(',', $operationSuplies[$counter]);
                    $expSupliesIds  = explode(',', $impSupliesIds);

                    $suppliesIds = MachineSupplie::whereIn('id', $expSupliesIds)->get();

                    // foreach ($suppliesIds as $suppliesId) {
                    //     $suppliesId->decrement('used', $request->quantity[$counter] * $request->length[$counter]);
                    //     $suppliesId->save();
                    // }
                }
            }
            $counter++;
        }

        //send notifications to users
        // for ($index = 0; $index < count($users); $index++) {
        //     $this->push_notification(['user_id' => $users[$index], 'url' => url('operation_orders')]);
        // }

        //send notifications to Responsable users of machine branch
        $machineBranch = DB::table('branches')->where('name', $store->name)->first();
        $rolesIds = DB::table('roles')->whereIn('name', ['factor_response', 'branch_factor_respons', 'safe_factor_response'])->pluck('id')->toArray();
        $usersRespons = DB::table('users')->where('branch_id', $machineBranch->id)->whereIn('role_id', $rolesIds)->pluck('id')->toArray();

        for ($index = 0; $index < count($usersRespons); $index++) {
            $this->push_notification(['user_id' => $usersRespons[$index], 'url' => url('operation_orders')]);
        }

        $operationOrder->tracks()->createMany([
            [
                'step_name' => 'warehouse_supervisor',
            ],
            [
                'step_name' => 'machine_manager',
            ],
            [
                'step_name' => 'production_manager',
            ],
            [
                'step_name' => 'store_manager',
            ],
        ]);

        session()->flash('success', __('site.added_successfully'));
        return redirect()->route('dashboard.operation_orders.index_out');
    }

    public function show(OperationOrder $operationOrder)
    {
        return view('dashboard.operation_orders.show', compact('operationOrder'));
    }
    
    public function showStore(OperationOrder $operationOrder)
    {
        return view('dashboard.operation_orders.showStore', compact('operationOrder'));
    }

    public function destroy($operationOrder)
    {
        $operationOrder = OperationOrder::find($operationOrder);

        if(!empty($operationOrder)) {
            $operationOrderDetails = OperationOrderDetail::where('operation_order_id', $operationOrder)->get();
            
            $machine = Machines::where('id', $operationOrder->machine_id)->first();
            $store = DB::table('stores')
                ->select('id', 'name')
                ->where('id', $machine->store_id)
                ->first();
    
    
            if (!@empty($operationOrder->operationOrderDetails)) {
                foreach ($operationOrder->operationOrderDetails as $operationOrderDetail) {
    
                    $operationOrderResult = OperationOrderResult::where('order_details_id', $operationOrderDetail->id)->first();
                    if ($operationOrderResult) {
                        $supplies_id = explode(',', $operationOrderDetail->operation_suplies_id);
                        $machine_supplies = MachineSupplie::where('machine_id', $operationOrder->machine_id)->whereIn('supplie_id', $supplies_id)->get();
                        if ($machine_supplies) {
                            foreach ($machine_supplies as $machine_supplie) {
                                $supply = Supplies::where('id', $machine_supplie->supplie_id)->first();
                                $machine_supplie->increment('used', $operationOrderDetail->supplie_quantity_used);
                                $machine_supplie->quantity = ceil($machine_supplie->used / $supply->used);
                                $machine_supplie->save();
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
                        $operationOrderResult->delete();
                        // $operationOrderResult->update(['is_deleted' => 1]);
                    }
    
                    $operationOrderDetail->delete();
                }
            }
    
            $operationOrder->delete();
            // $operationOrder->update(['is_deleted' => 1]);
        }
        
        session()->flash('success', __('site.deleted_successfully'));
        return back();
    }

    public function order_delete_detail($id)
    {
        $operationOrderDetail = OperationOrderDetail::where('id', $id)->first();
        $operationOrder = OperationOrder::where('id', $operationOrderDetail->operation_order_id)->first();
        $operationOrderResults = OperationOrderResult::where('order_details_id', $operationOrderDetail->id)->get();
        $machine = Machines::where('id', $operationOrder->machine_id)->first();
        $store = DB::table('stores')
            ->select('id', 'name')
            ->where('id', $machine->store_id)
            ->first();

        $quantity = Quantity::where('ownerable_type', 'App\Models\Store')
            ->where('ownerable_id', $store->id)
            ->where('item_id', $operationOrderDetail->item_id)
            ->first();

        if ($operationOrderDetail->operationOrderResults()->exists()) {
            session()->flash('error', __('site.can_not_delete_related_items'));
            return back();
        } else {
            // $quantity->increment('quantity', $operationOrderDetail->old_item_quantity);
            // $quantity->save();
            $operationOrderDetail->delete();

            session()->flash('success', __('site.deleted_successfully'));
            return back();
        }
        // dd($operationOrderDetail->operationOrderResults);







    }

    public function getItemsByMachine(Request $request)
    {
        if (!$request->machine_id) {
            $html = '<option value="">' . trans('site.items') . '</option>';
        } else {
            $html = '';
            $machineItems = MachineItem::where('machine_id', $request->machine_id)->get();
            foreach ($machineItems as $machineItem) {
                $html .= '<option value="' . $machineItem->Item['id'] . '">' . $machineItem->Item['name'] . '</option>';
            }
        }

        return response()->json(['html' => $html]);
    }
    public function getInGroupsByMachine(Request $request)
    {
        $groupIds = '';
        if (!$request->machine_id) {
            $html = '<option value="">' . trans('site.items') . '</option>';
        } else {
            $html = '<option value="">المجموعات</option>';
            $machineGroups = MachineGroup::where('machine_id', $request->machine_id)
                ->where('type', 'in')
                ->get();
            foreach ($machineGroups as $machineGroup) {
                $groupIds = explode(',', $machineGroup->group_id);
                $groups = Group::whereIn('id', $groupIds)->latest()->get();
                foreach ($groups as $group) {
                    $html .= '<option value="' . $group->id . '">' . $group->name . '</option>';
                }
            }
        }

        return response()->json(['html' => $html]);
    }
    public function getOutGroupsByMachine(Request $request)
    {
        if (!$request->machine_id) {
            $html = '<option value="">' . trans('site.items') . '</option>';
        } else {
            $html = '<option value="">المجموعات</option>';
            $machineGroups = MachineGroup::where('machine_id', $request->machine_id)
                ->where('type', 'out')
                ->get();
            foreach ($machineGroups as $machineGroup) {
                $groupIds = explode(',', $machineGroup->group_id);
                $groups = Group::whereIn('id', $groupIds)->latest()->get();
                foreach ($groups as $group) {
                    $html .= '<option value="' . $group->id . '">' . $group->name . '</option>';
                }
            }
        }

        return response()->json(['html' => $html]);
    }

    public function getItemsByGroup(Request $request)
    {
        if (!$request->out_group_id) {
            $html = '<option value="">' . trans('site.items') . '</option>';
        } else {
            $html = '<option value="">الخامات</option>';
            $items = Item::where('group_id', $request->out_group_id)->get();
            foreach ($items as $item) {
                $html .= '<option value="' . $item->id . '">' . $item->name . '</option>';
            }
        }

        return response()->json(['html' => $html]);
    }

    public function getSuppliesByMachine(Request $request)
    {
        if (!$request->machine_id) {
            $html = '<option value="">' . trans('site.items') . '</option>';
        } else {
            $html = '';


            $selectedOptions = $request->selectedOptions ? explode(',', $request->selectedOptions) : [];

            $machineSupplies = MachineSupplie::where("machine_id", $request->machine_id)
                ->where("used", ">", 1)
                ->get();
                

            foreach ($machineSupplies as $machineSupplie) {

                $isSelected = in_array($machineSupplie->id, $selectedOptions) ? 'selected' : '';



                $html .= '<option data-used="' . $machineSupplie->used . '" value="' . $machineSupplie->id . '" ' . $isSelected .'> ' . $machineSupplie->Supplie['name'] . '' . $machineSupplie->Supplie['id'] . '  : المتبقي  ' . number_format($machineSupplie->used, 2, '.', '') . ' </option>';
            }
        }

        return response()->json(['html' => $html]);
    }


    public function getMachineItemQnt(Request $request)
    {
        if (!$request->item_id) {
            $html = 'اختر الخامة المستخدمة';
        } else {
            $html = '';
            $machineItem =  MachineItem::where('machine_id', $request->machine_id)
                ->where('item_id', $request->item_id)
                ->first();
            $quantity = $machineItem->quantity;
        }
        return response()->json(['quantity' => $quantity]);
    }

    public function getItemQuantity(Request $request)
    {
        if (!$request->item_id) {
            $html = '<option value="">' . trans('site.items') . '</option>';
        } else {
            $html = '';

            $machine = Machines::where('id', $request->machine_id)->first();

            $store = DB::table('stores')
                ->select('id', 'name')
                ->where('id', $machine->store_id)
                ->first();

            $itemQuantity = Quantity::where('ownerable_type', 'App\Models\Store')
                ->where('ownerable_id', $store->id)
                ->where('item_id', $request->item_id)
                ->first();

            if (!$itemQuantity) {
                $quantity = 'لايوجد كمية في هذا المخزن';
            } else {
                $quantity = $itemQuantity->quantity;
            }
        }

        return response()->json(['quantity' => $quantity]);
    }

    public function updateConfirm(Request $request)
    {
        // $counter = 0;
        $requestConfirm = 0;
        if(isset($request->resource)) {
            foreach ($request->resource as $res) {
                $resource = OperationOrder::where('out_operation', 1)->where('id', $res['itemId'])->first();
                // dd($request->resource);
                if ($resource) {
                    if (Arr::exists($res, 'confirmed')) {
                        if ($res['confirmed'] == 'on')
                            $res['confirmed'] = 1;
                        else
                            $res['confirmed'] = 0;

                        // $dt = new DateTime;
                        $resource->update([
                            'out_confirmed' => $res['confirmed'],
                            'confirm_notes' => $res['confirm_notes'],
                            'confirm_user_id' => auth()->user()->id,
                            'confirmed_at' => date('Y-m-d h:i:s'),
                        ]);
                    }
                    if (Arr::exists($res, 'confirm_notes')) {

                        // $dt = new DateTime;
                        $resource->update([
                            'confirm_notes' => $res['confirm_notes'],
                        ]);
                    }
                }
            }
        } else {
            session()->flash('error', 'بالرجاء تحديد أمر شغل للتأكيد');
            return redirect()->route('dashboard.operation_orders.index_out');
        }
        session()->flash('success', __('site.confirmed_successfully'));
        return redirect()->route('dashboard.operation_orders.index_out');
    }

    public function showCompleteOut(int $id)
    {
        $resource = OperationOrder::findOrFail($id);

        $machines = Machines::all();
        $employees = Employee::where('active', 1)->where('branch_id', 6)->latest()->get();

        $users = User::where('role_id', 7)->get();
        $supervisor_store = User::where('role_id', 9)->get();
        $admins = User::where('role_id', 8)->get();
        $clients = DB::table('clients')->latest()->get();

        $supplieTypes = SupplieTypes::all();
        $groups = Group::latest()->get();
        $machineTypes = MachineTypes::latest()->get();

        $machineSupplies = MachineSupplie::where("machine_id", $resource->machine_id)
            ->where("used", ">", 1)
            ->get();

        return view('dashboard.operation_orders.complete_out', compact('machineSupplies', 'resource', 'machines', 'users', 'machineTypes', 'employees', 'groups', 'supplieTypes', 'admins', 'supervisor_store', 'clients'));
    }
    //

    public function updateCompleteOut(Request $request, int $id)
    {
        $request->validate([
            'user_id' => 'required',
            'supervisor_process' => 'required',
        ]);

        $operationOrder = OperationOrder::findOrFail($id);

        $request_data = $request->except(['employee_id']);

        /**
         * There are array of user
         */
        if (isset($request['user_id'])) {
            $users = $request->input('user_id');
            $request_data['user_id'] = implode(',', $users);
        }

        if (isset($request['supervisor_process'])) {
            $users = $request->input('supervisor_process');
            $request_data['supervisor_process'] = implode(',', $users);
        }
        if (isset($request['supplies_id'])) {
            $counter = 0;
            foreach ($request->operation_order_detail_id as $item) {
                $operationOrderDetail = OperationOrderDetail::findOrFail($request->operation_order_detail_id[$counter]);

                $impSupliesIds = implode(',', $request->supplies_id[$operationOrderDetail->id]);
                $expSupliesIds = explode(',', $impSupliesIds);

                $suppliesIds = MachineSupplie::whereIn('id', $expSupliesIds)->pluck('supplie_id')->toArray();
                $operationOrderDetail->operation_suplies_id = implode(',', $suppliesIds);

                $suppliesused = MachineSupplie::whereIn('id', $expSupliesIds)->pluck('used')->toArray();
                $operationOrderDetail->supplie_quantity_pre_used = implode(',', $suppliesused);
                $operationOrderDetail->save();

                $counter++;
            }
        }

        $operationOrder->update(['is_complete' => 1, 'created_by' => auth()->user()->id]);
        $operationOrder->update(['machine_access' => 1]);
        $operationOrder->update($request_data);

        if (isset($request['user_id'])) {
            for ($index = 0; $index < count($users); $index++) {
                $this->push_notification(['user_id' => $users[$index], 'url' => url('operation_orders')]);
            }
        }

        session()->flash('success', 'تم استكمال البيانات بنجاح');
        return redirect()->route('dashboard.operation_orders.index_out');
        // return redirect()->route('dashboard.operation_orders.index_out', ['type' => 'complete']);
    }

    
    // public function showCompleteOut(int $id) {
    //     $resource = OperationOrder::findOrFail($id);

    //     $machines = Machines::all();
    //     $employees = Employee::where('active', 1)->where('branch_id', 6)->latest()->get();

    //     $users = User::where('role_id', 7)->get();
    //     $supervisor_store = User::where('role_id',9)->get();
    //     $admins = User::where('role_id', 8)->get();
    //     $clients = DB::table('clients')->latest()->get();
        
    //     $supplieTypes = SupplieTypes::all();
    //     $groups = Group::latest()->get();
    //     $machineTypes = MachineTypes::latest()->get();

    //     $machineSupplies = MachineSupplie::where("machine_id", $resource->machine_id)
    //         ->where("used", ">", 1)
    //         ->get();

    //     return view('dashboard.operation_orders.complete_out', compact('machineSupplies', 'resource', 'machines', 'users', 'machineTypes', 'employees', 'groups', 'supplieTypes', 'admins', 'supervisor_store', 'clients'));
    // }
    // //
    
    // public function updateCompleteOut(Request $request, int $id) {        
    //     $request->validate([
    //         'user_id' => 'required',
    //         'supervisor_process' => 'required',
    //     ]);

    //     $operationOrder = OperationOrder::findOrFail($id);

    //     $request_data = $request->except(['employee_id']);
        
    //     /**
    //     * There are array of user
    //     */
    //     if(isset($request['user_id'])) {
    //         $users = $request->input('user_id');
    //         $request_data['user_id'] = implode(',', $users);
    //     }

    //     if(isset($request['supervisor_process'])) {
    //         $users = $request->input('supervisor_process');
    //         $request_data['supervisor_process'] = implode(',', $users);
    //     }
        
    //     // if(isset($request['supervisor_store'])) {
    //     //     $users = $request->input('supervisor_store');
    //     //     $request_data['supervisor_store'] = implode(',', $users);
    //     // }

    //     if(isset($request['supplies_id'])) {
    //         $counter = 0;
    //         foreach($request->operation_order_detail_id as $item) {
    //             $operationOrderDetail = OperationOrderDetail::findOrFail($request->operation_order_detail_id[$counter]);

    //             $impSupliesIds = implode(',', $request->supplies_id[$operationOrderDetail->id]);
    //             $expSupliesIds = explode(',', $impSupliesIds);

    //             $suppliesIds = MachineSupplie::whereIn('id', $expSupliesIds)->pluck('supplie_id')->toArray();
    //             $operationOrderDetail->operation_suplies_id = implode(',', $suppliesIds);
                
    //             $suppliesused = MachineSupplie::whereIn('id', $expSupliesIds)->pluck('used')->toArray();
    //             $operationOrderDetail->supplie_quantity_pre_used = implode(',', $suppliesused);
    //             $operationOrderDetail->save();

    //             $counter++;
    //         }
    //     }

    //     $operationOrder->update(['is_complete' => 1, 'created_by' => auth()->user()->id]);
    //     $operationOrder->update(['machine_access' => 1]);

    //     $operationOrder->update($request_data);

    //     if(isset($request['user_id'])) {
    //         for($index = 0; $index < count($users); $index++) {
    //             $this->push_notification(['user_id' => $users[$index], 'url' => url('operation_orders')]);
    //         }
    //     }

    //     session()->flash('success', 'تم استكمال البيانات بنجاح');
    //     return redirect()->route('dashboard.operation_orders.index_out');
    //     // return redirect()->route('dashboard.operation_orders.index_out', ['type' => 'complete']);
    // }
    // //
    
    public function machineAccess(Request $request, int $id) {
        $operationOrder = OperationOrder::findOrFail($id);

        $operationOrder->update(['machine_access' => 1, 'notes2' => $request->notes2 ?? '', 'store_employees' => implode(',', $request->store_employees) ?? 0]);

        $operationOrder->tracks()->where('step_name', 'warehouse_supervisor')->update([
            'status' => 'approved',
            'user_id' => auth()->user()->id,
            'notes' => $request->notes2 ?? null,
            'action_at' => now(),
        ]);
    
        session()->flash('success', 'تم السماح للماكينة بالاستكمال');
        return back();
    }
    //

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
    
    // Show the edit form for out_operation orders
    public function editOut($id)
    {
        $operationOrder = OperationOrder::where('id', $id)->where('out_operation', 1)->firstOrFail();
        $machines = Machines::all();
        $employees = Employee::where('active', 1)->where('branch_id', 6)->latest()->get();
        $users = User::where('role_id', 7)->get();
        $supervisor_store = User::where('role_id', 9)->get();
        $admins = User::where('role_id', 8)->get();
        $clients = DB::table('clients')->latest()->get();
        $supplieTypes = SupplieTypes::all();
        $groups = Group::latest()->get();
        $machineTypes = MachineTypes::latest()->get();
        $operationOrderDetails = OperationOrderDetail::where('operation_order_id', $id)->get();
        $machineSupplies = MachineSupplie::where("machine_id", $operationOrder->machine_id)
        ->where("used", ">", 1)
        ->get();
        $supplies = Supplies::all();
        return view('dashboard.operation_orders.edit_out', compact(
            'operationOrder',
            'machines',
            'users',
            'supervisor_store',
            'admins',
            'clients',
            'supplieTypes',
            'groups',
            'machineTypes',
            'employees',
            'operationOrderDetails',
            'supplies',
            'machineSupplies'
        ));
    }

    // Handle the update for out_operation orders
    public function updateOut(Request $request, $id)
    {
        $operationOrder = OperationOrder::where('id', $id)->where('out_operation', 1)->firstOrFail();

        // Check if current user is supervisor_store
        $currentUserId = auth()->user()->id;
        $supervisorStoreIds = explode(',', $operationOrder->supervisor_store);
        $isSupervisorStore = in_array($currentUserId, $supervisorStoreIds);

        // Validation for external operations - different rules for supervisor_store
        if ($isSupervisorStore) {
            // For supervisor_store users, only validate the fields they can actually edit
            $validationRules = [
                'date' => 'required',
                'store_employees' => 'required',
            ];
        } else {
            // For other users, validate all fields
            $validationRules = [
                'date' => 'required',
                'machine_type_id' => 'required',
                'machine_id' => 'required',
                'user_id' => 'required',
                'supervisor_store' => 'required',
                'supervisor_process' => 'required',
                'client_name' => 'required',
                'old_item_unit' => 'required',
                'out_item_unit' => 'required',
                'store_employees' => 'required',
                'quantity.*' => 'required|numeric|min:0',
                'old_item_quantity.*' => 'required|numeric|min:0',
                'length.*' => 'required|numeric|min:0',
                'width.*' => 'required|numeric|min:0',
            ];
        }

        $request->validate($validationRules);

        // Prepare main operation order data with fallback to existing values
        $requestData = [];

        // Handle fields that might be empty - use existing values if not provided
        $requestData['date'] = $request->input('date') ?: $operationOrder->date;
        $requestData['machine_type_id'] = $request->input('machine_type_id') ?: $operationOrder->machine_type_id;
        $requestData['machine_id'] = $request->input('machine_id') ?: $operationOrder->machine_id;
        $requestData['client_name'] = $request->input('client_name') ?: $operationOrder->client_name;
        $requestData['old_item_unit'] = $request->input('old_item_unit') ?: $operationOrder->old_item_unit;
        $requestData['out_item_unit'] = $request->input('out_item_unit') ?: $operationOrder->out_item_unit;
        $requestData['notes'] = $request->input('notes');
        $requestData['notes2'] = $request->input('notes2');

        // Handle array fields with fallback to existing values
        if (!empty($request['user_id'])) {
            $users = $request->input('user_id');
            $requestData['user_id'] = implode(',', $users);
        } else {
            $requestData['user_id'] = $operationOrder->user_id;
        }

        if (!empty($request['supervisor_store'])) {
            $supervisorStore = $request->input('supervisor_store');
            $requestData['supervisor_store'] = implode(',', $supervisorStore);
        } else {
            $requestData['supervisor_store'] = $operationOrder->supervisor_store;
        }

        if (!empty($request['supervisor_process'])) {
            $supervisorProcess = $request->input('supervisor_process');
            $requestData['supervisor_process'] = implode(',', $supervisorProcess);
        } else {
            $requestData['supervisor_process'] = $operationOrder->supervisor_process;
        }

        if (!empty($request['store_employees'])) {
            $storeEmployees = $request->input('store_employees');
            $requestData['store_employees'] = implode(',', $storeEmployees);
        } else {
            $requestData['store_employees'] = $operationOrder->store_employees;
        }

        // Get machine and store info
        $machine = DB::table('machines')->where('id', $requestData['machine_id'])->select(['id', 'store_id'])->first();
        $store = DB::table('stores')->where('id', $machine->store_id)->select(['id', 'name'])->first();

        // Update main operation order
        $operationOrder->update($requestData);

        // Handle workflow states
        if ($operationOrder->is_complete == -1) {
            $operationOrder->update(['is_complete' => 0]);
        }

        $user = Auth::user()->id;
        if ($operationOrder->machine_edit == 1 && $operationOrder->created_by == $user) {
            $operationOrder->update(['machine_access' => 1, 'machine_edit' => 0]);
        }

        // Handle OperationOrderDetails - following the robust pattern from regular update
        if (!empty($request->item_name)) {
            foreach ($request->item_name as $index => $item) {

                // Check quantity availability for external operations
                if (!empty($request->old_item_quantity[$index])) {
                    $dbInQuantity = DB::table('quantities')->where('ownerable_type', 'App\Models\Store')
                        ->where('ownerable_id', $store->id)
                        ->where('item_id', $request->item_id[$index] ?? null)
                        ->first();

                    if ($dbInQuantity && $request->old_item_quantity[$index] > $dbInQuantity->quantity) {
                        session()->flash('error', 'لا يمكن ان تكون كمية الخامة المستخدمة اكبر من المتاحة للألة');
                        return redirect()->back();
                    }
                }

                // Create new detail or update existing
                if (empty($request->operation_order_detail_id[$index])) {
                    // Create new detail
                    $orderDetailCreate = OperationOrderDetail::create([
                        'operation_order_id' => $operationOrder->id,
                        'group_id' => $request->group_id[$index] ?? null,
                        'item_id' => $request->item_id[$index] ?? null,
                        'item_name' => $request->item_name[$index],
                        'old_in_balance' => !empty($dbInQuantity) ? $dbInQuantity->quantity : NULL,
                        'old_item_quantity' => $request->old_item_quantity[$index],
                        'out_item_name' => $request->out_item_name[$index],
                        'quantity' => $request->quantity[$index],
                        'length' => $request->length[$index],
                        'width' => $request->width[$index],
                        'old_item_supp_quantity' => $request->old_item_supp_quantity[$index] ?? $request->quantity[$index],
                        'supplie_quantity_used' => ($request->old_item_supp_quantity[$index] ?? $request->quantity[$index]) * $request->length[$index],
                        'out_group_id' => $request->out_group_id[$index] ?? null,
                        'out_name' => $request->out_name[$index] ?? $request->out_item_name[$index],
                        'price' => $request->price[$index] ?? 0,
                    ]);

                    // Handle output item if exists
                    if (!empty($request->out_item_id[$index])) {
                        $dbOutQuantity = DB::table('quantities')->where('ownerable_type', 'App\Models\Store')
                            ->where('ownerable_id', $store->id)
                            ->where('item_id', $request->out_item_id[$index])
                            ->first();

                        $orderDetailCreate->out_item_id = $request->out_item_id[$index];
                        $orderDetailCreate->old_out_balance = !empty($dbOutQuantity) ? $dbOutQuantity->quantity : NULL;
                        $orderDetailCreate->save();
                    }

                    // Handle supplies
                    if (!empty($request->operation_suplies_id[$index])) {
                        $operationSuplies = $request->input('operation_suplies_id');
                        $impSupliesIds = implode(',', $operationSuplies[$index]);
                        $expSupliesIds = explode(',', $impSupliesIds);
                        $suppliesIds = DB::table('machine_supplies')->whereIn('id', $expSupliesIds)->pluck('supplie_id')->toArray();
                        $suppliesused = DB::table('machine_supplies')->whereIn('id', $expSupliesIds)->pluck('used')->toArray();
                        $suppliesIds = implode(',', $suppliesIds);
                        $suppliesused = implode(',', $suppliesused);
                        $orderDetailCreate->update([
                            'operation_suplies_id' => $suppliesIds,
                            'supplie_quantity_pre_used' => $suppliesused,
                        ]);
                    }

                } else {
                    // Update existing detail
                    $updateData = [
                        'item_name' => $request->item_name[$index],
                        'old_item_quantity' => $request->old_item_quantity[$index],
                        'out_item_name' => $request->out_item_name[$index],
                        'quantity' => $request->quantity[$index],
                        'length' => $request->length[$index],
                        'width' => $request->width[$index],
                        'old_item_supp_quantity' => $request->old_item_supp_quantity[$index] ?? $request->quantity[$index],
                        'supplie_quantity_used' => ($request->old_item_supp_quantity[$index] ?? $request->quantity[$index]) * $request->length[$index],
                        'price' => $request->price[$index] ?? 0,
                    ];

                                        // Handle supplies for existing detail - only for creator (owner)
                    if (Auth::user()->id == $operationOrder->created_by) {
                        // Check for supplies data - try both index and detail_id as keys
                        $suppliesData = null;
                        $suppliesKey = null;

                        // First try using index (for supervisor form)
                        if (!empty($request->operation_suplies_id[$index])) {
                            $suppliesData = $request->operation_suplies_id[$index];
                            $suppliesKey = $index;
                        }
                        // Then try using detail_id (for owner form)
                        elseif (!empty($request->operation_suplies_id[$request->operation_order_detail_id[$index]])) {
                            $suppliesData = $request->operation_suplies_id[$request->operation_order_detail_id[$index]];
                            $suppliesKey = $request->operation_order_detail_id[$index];
                        }

                        if ($suppliesData) {
                            // User provided new supplies - update them with proper processing
                            $operationSuplies = $request->input('operation_suplies_id');

                            $impSupliesIds = implode(',', $suppliesData);
                            $expSupliesIds = explode(',', $impSupliesIds);
                            $suppliesIds = DB::table('machine_supplies')->whereIn('id', $expSupliesIds)->pluck('supplie_id')->toArray();
                            $suppliesused = DB::table('machine_supplies')->whereIn('id', $expSupliesIds)->pluck('used')->toArray();
                            $suppliesIds = implode(',', $suppliesIds);
                            $suppliesused = implode(',', $suppliesused);

                            $updateData['operation_suplies_id'] = $suppliesIds;
                            $updateData['supplie_quantity_pre_used'] = $suppliesused;
                        } else {
                            // No supplies provided - clear the supplies
                            $updateData['operation_suplies_id'] = null;
                            $updateData['supplie_quantity_pre_used'] = null;
                        }
                    }
                    // For supervisor_store users, operation_suplies_id is not updated, so existing supplies are preserved

                    OperationOrderDetail::where('id', $request->operation_order_detail_id[$index])->update($updateData);
                }
            }
        }

        // Handle deletions
        $selectedItems = $request->input('selected_items');
        if (!empty($selectedItems)) {
            DB::table('operation_order_details')->whereIn('id', $selectedItems)->delete();
        }

        // Send notifications
        if (!empty($request['user_id'])) {
            $users = is_array($request->input('user_id')) ? $request->input('user_id') : explode(',', $request->input('user_id'));
            for ($index = 0; $index < count($users); $index++) {
                $this->push_notification(['user_id' => $users[$index], 'url' => url('operation_orders')]);
            }
        }

        // Send notifications to responsible users
        $machineBranch = DB::table('branches')->where('name', $store->name)->first();
        if ($machineBranch) {
            $rolesIds = DB::table('roles')->whereIn('name', ['factor_response', 'branch_factor_respons', 'safe_factor_response'])->pluck('id')->toArray();
            $usersRespons = DB::table('users')->where('branch_id', $machineBranch->id)->whereIn('role_id', $rolesIds)->pluck('id')->toArray();

            for ($index = 0; $index < count($usersRespons); $index++) {
                $this->push_notification(['user_id' => $usersRespons[$index], 'url' => url('operation_orders')]);
            }
        }

        session()->flash('success', __('site.updated_successfully'));
        return redirect()->route('dashboard.operation_orders.index_out');
    }


    // public function updateOut(Request $request, $id)
    // {
    //     // return $request;
    //     $operationOrder = OperationOrder::where('id', $id)->where('out_operation', 1)->firstOrFail();

    //     // Check if current user is supervisor_store
    //     $currentUserId = auth()->user()->id;
    //     $supervisorStoreIds = explode(',', $operationOrder->supervisor_store);
    //     $isSupervisorStore = in_array($currentUserId, $supervisorStoreIds);

    //     // Validation for external operations - different rules for supervisor_store
    //     if ($isSupervisorStore) {
    //         // For supervisor_store users, only validate the fields they can actually edit
    //         $validationRules = [
    //             'date' => 'required',
    //             'store_employees' => 'required',
    //         ];
    //     } else {
    //         // For other users, validate all fields
    //         $validationRules = [
    //             'date' => 'required',
    //             'machine_type_id' => 'required',
    //             'machine_id' => 'required',
    //             'user_id' => 'required',
    //             'supervisor_store' => 'required',
    //             'supervisor_process' => 'required',
    //             'client_name' => 'required',
    //             'old_item_unit' => 'required',
    //             'out_item_unit' => 'required',
    //             'store_employees' => 'required',
    //             'quantity.*' => 'required|numeric|min:0',
    //             'old_item_quantity.*' => 'required|numeric|min:0',
    //             'length.*' => 'required|numeric|min:0',
    //             'width.*' => 'required|numeric|min:0',
    //         ];
    //     }

    //     $request->validate($validationRules);

    //     // Prepare main operation order data with fallback to existing values
    //     $requestData = [];

    //     // Handle fields that might be empty - use existing values if not provided
    //     $requestData['date'] = $request->input('date') ?: $operationOrder->date;
    //     $requestData['machine_type_id'] = $request->input('machine_type_id') ?: $operationOrder->machine_type_id;
    //     $requestData['machine_id'] = $request->input('machine_id') ?: $operationOrder->machine_id;
    //     $requestData['client_name'] = $request->input('client_name') ?: $operationOrder->client_name;
    //     $requestData['old_item_unit'] = $request->input('old_item_unit') ?: $operationOrder->old_item_unit;
    //     $requestData['out_item_unit'] = $request->input('out_item_unit') ?: $operationOrder->out_item_unit;
    //     $requestData['notes'] = $request->input('notes');
    //     $requestData['notes2'] = $request->input('notes2');

    //     // Handle array fields with fallback to existing values
    //     if (!empty($request['user_id'])) {
    //         $users = $request->input('user_id');
    //         $requestData['user_id'] = implode(',', $users);
    //     } else {
    //         $requestData['user_id'] = $operationOrder->user_id;
    //     }

    //     if (!empty($request['supervisor_store'])) {
    //         $supervisorStore = $request->input('supervisor_store');
    //         $requestData['supervisor_store'] = implode(',', $supervisorStore);
    //     } else {
    //         $requestData['supervisor_store'] = $operationOrder->supervisor_store;
    //     }

    //     if (!empty($request['supervisor_process'])) {
    //         $supervisorProcess = $request->input('supervisor_process');
    //         $requestData['supervisor_process'] = implode(',', $supervisorProcess);
    //     } else {
    //         $requestData['supervisor_process'] = $operationOrder->supervisor_process;
    //     }

    //     if (!empty($request['store_employees'])) {
    //         $storeEmployees = $request->input('store_employees');
    //         $requestData['store_employees'] = implode(',', $storeEmployees);
    //     } else {
    //         $requestData['store_employees'] = $operationOrder->store_employees;
    //     }

    //     // Get machine and store info
    //     $machine = DB::table('machines')->where('id', $requestData['machine_id'])->select(['id', 'store_id'])->first();
    //     $store = DB::table('stores')->where('id', $machine->store_id)->select(['id', 'name'])->first();

    //     // Update main operation order
    //     $operationOrder->update($requestData);

    //     // Handle workflow states
    //     if ($operationOrder->is_complete == -1) {
    //         $operationOrder->update(['is_complete' => 0]);
    //     }

    //     $user = Auth::user()->id;
    //     if ($operationOrder->machine_edit == 1 && $operationOrder->created_by == $user) {
    //         $operationOrder->update(['machine_access' => 1, 'machine_edit' => 0]);
    //     }

    //     // Handle OperationOrderDetails - following the robust pattern from regular update
    //     $counter = 0;
    //     if (!empty($request->item_name)) {
    //         foreach ($request->item_name as $item) {

    //             // Check quantity availability for external operations
    //             if (!empty($request->old_item_quantity[$counter])) {
    //                 $dbInQuantity = DB::table('quantities')->where('ownerable_type', 'App\Models\Store')
    //                     ->where('ownerable_id', $store->id)
    //                     ->where('item_id', $request->item_id[$counter] ?? null)
    //                     ->first();

    //                 if ($dbInQuantity && $request->old_item_quantity[$counter] > $dbInQuantity->quantity) {
    //                     session()->flash('error', 'لا يمكن ان تكون كمية الخامة المستخدمة اكبر من المتاحة للألة');
    //                     return redirect()->back();
    //                 }
    //             }

    //             // Create new detail or update existing
    //             if (empty($request->operation_order_detail_id[$counter])) {
    //                 // Create new detail
    //                 $orderDetailCreate = OperationOrderDetail::create([
    //                     'operation_order_id' => $operationOrder->id,
    //                     'group_id' => $request->group_id[$counter] ?? null,
    //                     'item_id' => $request->item_id[$counter] ?? null,
    //                     'item_name' => $request->item_name[$counter],
    //                     'old_in_balance' => !empty($dbInQuantity) ? $dbInQuantity->quantity : NULL,
    //                     'old_item_quantity' => $request->old_item_quantity[$counter],
    //                     'out_item_name' => $request->out_item_name[$counter],
    //                     'quantity' => $request->quantity[$counter],
    //                     'length' => $request->length[$counter],
    //                     'width' => $request->width[$counter],
    //                     'old_item_supp_quantity' => $request->old_item_supp_quantity[$counter] ?? $request->quantity[$counter],
    //                     'supplie_quantity_used' => ($request->old_item_supp_quantity[$counter] ?? $request->quantity[$counter]) * $request->length[$counter],
    //                     'out_group_id' => $request->out_group_id[$counter] ?? null,
    //                     'out_name' => $request->out_name[$counter] ?? $request->out_item_name[$counter],
    //                     'price' => $request->price[$counter] ?? 0,
    //                 ]);

    //                 // Handle output item if exists
    //                 if (!empty($request->out_item_id[$counter])) {
    //                     $dbOutQuantity = DB::table('quantities')->where('ownerable_type', 'App\Models\Store')
    //                         ->where('ownerable_id', $store->id)
    //                         ->where('item_id', $request->out_item_id[$counter])
    //                         ->first();

    //                     $orderDetailCreate->out_item_id = $request->out_item_id[$counter];
    //                     $orderDetailCreate->old_out_balance = !empty($dbOutQuantity) ? $dbOutQuantity->quantity : NULL;
    //                     $orderDetailCreate->save();
    //                 }

    //                 // Handle supplies
    //                 if (!empty($request->operation_suplies_id[$counter])) {
    //                     $operationSuplies = $request->input('operation_suplies_id');
    //                     $impSupliesIds = implode(',', $operationSuplies[$counter]);
    //                     $expSupliesIds = explode(',', $impSupliesIds);
    //                     $suppliesIds = DB::table('machine_supplies')->whereIn('id', $expSupliesIds)->pluck('supplie_id')->toArray();
    //                     $suppliesused = DB::table('machine_supplies')->whereIn('id', $expSupliesIds)->pluck('used')->toArray();
    //                     $suppliesIds = implode(',', $suppliesIds);
    //                     $suppliesused = implode(',', $suppliesused);
    //                     $orderDetailCreate->update([
    //                         'operation_suplies_id' => $suppliesIds,
    //                         'supplie_quantity_pre_used' => $suppliesused,
    //                     ]);
    //                 }

    //             } else {
    //                 // Update existing detail - only update operation_suplies_id if user is the creator
    //                 $updateData = [
    //                     'item_name' => $request->item_name[$counter],
    //                     'old_item_quantity' => $request->old_item_quantity[$counter],
    //                     'out_item_name' => $request->out_item_name[$counter],
    //                     'quantity' => $request->quantity[$counter],
    //                     'length' => $request->length[$counter],
    //                     'width' => $request->width[$counter],
    //                     'old_item_supp_quantity' => $request->old_item_supp_quantity[$counter] ?? $request->quantity[$counter],
    //                     'supplie_quantity_used' => ($request->old_item_supp_quantity[$counter] ?? $request->quantity[$counter]) * $request->length[$counter],
    //                     'price' => $request->price[$counter] ?? 0,
    //                 ];

    //                 // Only update operation_suplies_id if user is the creator (owner)
    //                 if (Auth::user()->id == $operationOrder->created_by && !empty($request->operation_suplies_id[$counter])) {
                    
    //                     $updateData['operation_suplies_id'] = implode(',', $request->operation_suplies_id[$counter]);
    //                 }

    //                 OperationOrderDetail::where('id', $request->operation_order_detail_id[$counter])->update($updateData);

    //                 // Handle supplies for existing detail - only for creator (owner)
    //                 if (!empty($request->operation_suplies_id[$counter]) && Auth::user()->id == $operationOrder->created_by) {
    //                     // User provided new supplies - update them with proper processing
    //                     $operationSuplies = $request->input('operation_suplies_id');
    //                     $impSupliesIds = implode(',', $operationSuplies[$counter]);
    //                     $expSupliesIds = explode(',', $impSupliesIds);
    //                     $suppliesIds = DB::table('machine_supplies')->whereIn('id', $expSupliesIds)->pluck('supplie_id')->toArray();
    //                     $suppliesused = DB::table('machine_supplies')->whereIn('id', $expSupliesIds)->pluck('used')->toArray();
    //                     $suppliesIds = implode(',', $suppliesIds);
    //                     $suppliesused = implode(',', $suppliesused);
    //                     OperationOrderDetail::where('id', $request->operation_order_detail_id[$counter])->update([
    //                         'operation_suplies_id' => $suppliesIds,
    //                         'supplie_quantity_pre_used' => $suppliesused,
    //                     ]);
    //                 }
    //                 // For supervisor_store users, operation_suplies_id is not updated, so existing supplies are preserved
    //             }
    //             $counter++;
    //         }
    //     }

    //     // Handle deletions
    //     $selectedItems = $request->input('selected_items');
    //     if (!empty($selectedItems)) {
    //         DB::table('operation_order_details')->whereIn('id', $selectedItems)->delete();
    //     }

    //     // Send notifications
    //     if (!empty($request['user_id'])) {
    //         $users = is_array($request->input('user_id')) ? $request->input('user_id') : explode(',', $request->input('user_id'));
    //         for ($index = 0; $index < count($users); $index++) {
    //             $this->push_notification(['user_id' => $users[$index], 'url' => url('operation_orders')]);
    //         }
    //     }

    //     // Send notifications to responsible users
    //     $machineBranch = DB::table('branches')->where('name', $store->name)->first();
    //     if ($machineBranch) {
    //         $rolesIds = DB::table('roles')->whereIn('name', ['factor_response', 'branch_factor_respons', 'safe_factor_response'])->pluck('id')->toArray();
    //         $usersRespons = DB::table('users')->where('branch_id', $machineBranch->id)->whereIn('role_id', $rolesIds)->pluck('id')->toArray();

    //         for ($index = 0; $index < count($usersRespons); $index++) {
    //             $this->push_notification(['user_id' => $usersRespons[$index], 'url' => url('operation_orders')]);
    //         }
    //     }

    //     session()->flash('success', __('site.updated_successfully'));
    //     return redirect()->route('dashboard.operation_orders.index_out');
    // }
    
    
    public function updateIsComplete($id)
    {
        $operationOrder = OperationOrder::findOrFail($id);

        // Set the operation to edit mode and reset completion status
        $operationOrder->update([
            'is_complete' => -1,
            'machine_edit' => 0,
            'machine_access' => 0  // Reset machine access when rolling back
        ]);

        // Log the action for audit trail
        \Log::info('Operation order ' . $id . ' rolled back to edit mode by user ' . auth()->user()->id);

        session()->flash('success', 'تم التراجع عن العملية ويمكن تعديلها الآن');
        return redirect()->route('dashboard.operation_orders.index_out');
    }

    // // Show the edit form for out_operation orders
    // public function editOut($id)
    // {
    //     $operationOrder = OperationOrder::where('id', $id)->where('out_operation', 1)->firstOrFail();
    //     $machines = Machines::all();
    //     $employees = Employee::where('active', 1)->where('branch_id', 6)->latest()->get();
    //     $users = User::where('role_id', 7)->get();
    //     $supervisor_store = User::where('role_id', 9)->get();
    //     $admins = User::where('role_id', 8)->get();
    //     $clients = DB::table('clients')->latest()->get();
    //     $supplieTypes = SupplieTypes::all();
    //     $groups = Group::latest()->get();
    //     $machineTypes = MachineTypes::latest()->get();
    //     $operationOrderDetails = OperationOrderDetail::where('operation_order_id', $id)->get();
    //     $machineSupplies = MachineSupplie::where("machine_id", $operationOrder->machine_id)
    //     ->where("used", ">", 1)
    //     ->get();
    //     $supplies = Supplies::all();
    //     return view('dashboard.operation_orders.edit_out', compact(
    //         'operationOrder',
    //         'machines',
    //         'users',
    //         'supervisor_store',
    //         'admins',
    //         'clients',
    //         'supplieTypes',
    //         'groups',
    //         'machineTypes',
    //         'employees',
    //         'operationOrderDetails',
    //         'machineSupplies',
    //         'supplies')
    //     );
    // }

    // // Handle the update for out_operation orders
    // public function updateOut(Request $request, $id)
    // {
    //     $operationOrder = OperationOrder::where('id', $id)->where('out_operation', 1)->firstOrFail();

    //     // Debug: Log the request data to see what's being sent
    //     \Log::info('updateOut request data:', $request->all());

    //     // Only validate fields that are present
    //     $rules = [
    //         'date' => 'sometimes|required',
    //         'machine_type_id' => 'sometimes|required',
    //         'machine_id' => 'sometimes|required',
    //         'user_id' => 'sometimes|required',
    //         'supervisor_store' => 'sometimes|required',
    //         'supervisor_process' => 'sometimes|required',
    //         'client_name' => 'sometimes|required',
    //         'old_item_unit' => 'sometimes|required',
    //         'out_item_unit' => 'sometimes|required',
    //     ];
    //     $this->validate($request, $rules);

    //     // Only update fields that are present
    //     $requestData = [];
    //     $fields = [
    //         'date',
    //         'machine_type_id',
    //         'machine_id',
    //         'client_name',
    //         'old_item_unit',
    //         'out_item_unit',
    //         'notes',
    //         'notes2',
    //     ];
    //     foreach ($fields as $field) {
    //         if ($request->has($field)) {
    //             $requestData[$field] = $request->input($field);
    //         }
    //     }
    //     // Handle array fields (like user_id, supervisor_store, etc.)
    //     if ($request->has('user_id')) {
    //         $users = $request->input('user_id');
    //         $requestData['user_id'] = is_array($users) ? implode(',', $users) : $users;
    //     }
    //     if ($request->has('supervisor_store')) {
    //         $supervisorStore = $request->input('supervisor_store');
    //         $requestData['supervisor_store'] = is_array($supervisorStore) ? implode(',', $supervisorStore) : $supervisorStore;
    //     }
    //     if ($request->has('supervisor_process')) {
    //         $supervisorProcess = $request->input('supervisor_process');
    //         $requestData['supervisor_process'] = is_array($supervisorProcess) ? implode(',', $supervisorProcess) : $supervisorProcess;
    //     }
    //     if ($request->has('store_employees')) {
    //         $storeEmployees = $request->input('store_employees');
    //         $requestData['store_employees'] = is_array($storeEmployees) ? implode(',', $storeEmployees) : $storeEmployees;
    //     }

    //     $operationOrder->update($requestData);
    //     if ($operationOrder->is_complete == -1) {
    //         $operationOrder->update(['is_complete' => 0]);
    //     }
    //     $user = Auth::user()->id;
    //     if ($operationOrder->machine_edit == 1 && $operationOrder->created_by == $user) {
    //         $operationOrder->update(['machine_access' => 1, 'machine_edit' => 0]);
    //     }
    //     // --- Handle OperationOrderDetails ---
    //     $fillable = [
    //         'operation_order_id',
    //         'group_id',
    //         'item_id',
    //         'item_name',
    //         'old_in_balance',
    //         'new_in_balance',
    //         'old_item_quantity',
    //         'out_item_id',
    //         'out_item_name',
    //         'old_out_balance',
    //         'new_out_balance',
    //         'operation_suplies_id',
    //         'out_group_id',
    //         'out_name',
    //         'price',
    //         'length',
    //         'width',
    //         'quantity',
    //         'is_special',
    //         'active',
    //         'old_item_supp_quantity',
    //         'supplie_quantity_used',
    //         'supplie_quantity_pre_used'
    //     ];

    //     $detailIds = $request->input('operation_order_detail_id', []);
    //     $selectedForDelete = $request->input('selected_items', []);

    //     // Debug: Log the detail IDs and selected items
    //     \Log::info('Detail IDs:', $detailIds);
    //     \Log::info('Selected for delete:', $selectedForDelete);

    //     // Prepare all detail arrays (handle missing fields)
    //     $detailsData = [];
    //     foreach ($fillable as $field) {
    //         $detailsData[$field] = $request->input($field, []);
    //     }

    //     // Special handling for supplies which come as nested arrays
    //     $suppliesData = $request->input('operation_suplies_id', []);
    //     if (!empty($suppliesData)) {
    //         // Convert nested array structure to flat array
    //         $flatSupplies = [];
    //         foreach ($suppliesData as $detailId => $supplies) {
    //             if (is_array($supplies)) {
    //                 $suppliesIds = MachineSupplie::whereIn('id', $supplies)->pluck('supplie_id')->toArray();
    //                 $flatSupplies[] = implode(',', $suppliesIds);
    //             } else {
    //                 $flatSupplies[] = [$supplies];
    //             }
    //         }
    //         $detailsData['operation_suplies_id'] = $flatSupplies;
    //     }
    //     // if (!empty($suppliesData)) {
    //     //     // Convert nested array structure to flat array
    //     //     $flatSupplies = [];
    //     //     foreach ($suppliesData as $detailId => $supplies) {
    //     //         if (is_array($supplies)) {
    //     //             $flatSupplies[] = $supplies;
    //     //         } else {
    //     //             $flatSupplies[] = [$supplies];
    //     //         }
    //     //     }
    //     //     $detailsData['operation_suplies_id'] = $flatSupplies;
    //     // }

    //     // Debug: Log the supplies data specifically
    //     \Log::info('Supplies data:', $detailsData['operation_suplies_id']);

    //     $totalRows = 0;
    //     foreach ($detailsData as $arr) {
    //         if (is_array($arr)) {
    //             $totalRows = max($totalRows, count($arr));
    //         }
    //     }

    //     // Update or delete existing details
    //     foreach ($detailIds as $i => $detailId) {
    //         if (in_array($detailId, $selectedForDelete)) {
    //             OperationOrderDetail::where('id', $detailId)->delete();
    //             continue;
    //         }
    //         $detail = OperationOrderDetail::find($detailId);
    //         if ($detail) {
    //             $updateData = [];
    //             foreach ($fillable as $field) {
    //                 if ($field === 'operation_order_id')
    //                     continue; // don't update parent id
    //                 $value = isset($detailsData[$field][$i]) ? $detailsData[$field][$i] : null;
    //                 if (is_array($value)) {
    //                     $value = implode(',', $value);
    //                 }
    //                 $updateData[$field] = $value;
    //             }

    //             // Debug: Log the update data for this detail
    //             \Log::info("Updating detail {$detailId} with data:", $updateData);

    //             $detail->update($updateData);
    //         }
    //     }

    //     // Add new details if any
    //     $numDetails = count($detailIds);
    //     for ($i = $numDetails; $i < $totalRows; $i++) {
    //         // Only add if at least one field is not null
    //         $hasData = false;
    //         foreach ($fillable as $field) {
    //             if ($field === 'operation_order_id')
    //                 continue;
    //             if (!empty($detailsData[$field][$i])) {
    //                 $hasData = true;
    //                 break;
    //             }
    //         }
    //         if ($hasData) {
    //             $createData = ['operation_order_id' => $operationOrder->id];
    //             foreach ($fillable as $field) {
    //                 if ($field === 'operation_order_id')
    //                     continue;
    //                 $value = isset($detailsData[$field][$i]) ? $detailsData[$field][$i] : null;
    //                 if (is_array($value)) {
    //                     $value = implode(',', $value);
    //                 }
    //                 $createData[$field] = $value;
    //             }
    //             if (!isset($createData['is_special']) || $createData['is_special'] === null) {
    //                 $createData['is_special'] = 0; // or 1, depending on your business logic
    //             }
    //             if (!isset($createData['active']) || $createData['active'] === null) {
    //                 $createData['active'] = 1; // or your default value
    //             }

    //             // Debug: Log the create data
    //             \Log::info("Creating new detail with data:", $createData);

    //             OperationOrderDetail::create($createData);
    //         }
    //     }

    //     session()->flash('success', __('site.updated_successfully'));
    //     return redirect()->route('dashboard.operation_orders.index_out');
    // }
    // public function updateIsComplete($id)
    // {
    //     $operationOrder = OperationOrder::findOrFail($id);
    //     $operationOrder->update(['is_complete' => -1]);
    //     session()->flash('success', __('site.updated_successfully'));
    //     return redirect()->route('dashboard.operation_orders.index_out');
    // }
    
}