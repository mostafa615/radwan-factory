<?php

namespace App\Http\Controllers\Dashboard;

use App\Department;
use App\Student;
use App\Admin;
use App\Reposite;
use App\Machines;
use App\MachineItem;
use App\MachineSupplie;
use App\Item;
use App\Supplies;
use App\MachineTypes;
use App\Branch;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\TrackingMachineSupplies;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables as FacadesDataTables;
use Storage;

class MachineSupplieController extends Controller
{

    public function __construct(){
        $this->middleware(['ability:admin,reade_machine_supplies'])->only('index');
        $this->middleware(['ability:admin,create_machine_supplies'])->only('create');
        $this->middleware(['ability:admin,update_machine_supplies'])->only('edit');
        $this->middleware(['ability:admin,delete_machine_supplies'])->only('destroy');

    }


    public function getData(Request $request) {
        $query = MachineSupplie::query()->select('*', 'machine_supplies.id as machine_supplie_id');


        return FacadesDataTables::eloquent($query->with(['supplie','machine'])->latest('machine_supplies.updated_at'))
                        ->addColumn('action', function(MachineSupplie $machineSupplie) {
                            $type = "action";
                            return view("dashboard.machine_supplies.action", compact("machineSupplie", "type"));
                        })
                        ->addColumn('machine_name', function(MachineSupplie $machineSupplie){
                            return optional($machineSupplie->machine)->name;
                        })
                        ->addColumn('supplie_name', function(MachineSupplie $machineSupplie){
                            return optional($machineSupplie->supplie)->name;
                        })
                        
                        ->addColumn('supplie_used', function (MachineSupplie $machineSupplie) {
                            return optional($machineSupplie->supplie)->used;
                        })
                        ->rawColumns(['action', 'machine_name', 'supplie_name'])
                        ->toJson();
    }

    public function index(Request $request)
    {
        $query = MachineSupplie::query();

        $query->with(['Machine', 'Supplie'])->latest()->get();

        return view('dashboard.machine_supplies.index');
    }

    public function create()
    {
        $machines = Machines::get();
        $supplies = Supplies::get();
        $machineTypes = MachineTypes::latest()->get();

        return view('dashboard.machine_supplies.create' , compact('machines' , 'machineTypes', 'supplies'));
    }
    
    
    /**
     * Momaher
     */


    public function store(Request $request)
    {
        $request->validate([
            'machine_id' => 'required',
            'supplie_id' => 'required',
            'quantity' => 'required',
            'date' => 'required',
            'notes',
        ]);

        $request_data = $request->all();

        DB::beginTransaction();
        $supplie = Supplies::where('id', $request->supplie_id)->first();
        $request_data['date'] = ($request['date']);
        $request_data['notes'] = ($request['notes']);
        $request_data['transfer_quantity'] = ($request['quantity']);
        $request_data['used'] = ($supplie->used);


        $machineSupplie = MachineSupplie::where('machine_id', $request['machine_id'])->where('supplie_id', $request['supplie_id'])->first();

        $trackingMachineSupplie = TrackingMachineSupplies::where('date', $request['date'])
            ->where('supplie_id', $request['supplie_id'])
            ->where('machine_id', $request['machine_id'])
            ->latest()->first();

        $new_track = TrackingMachineSupplies::create([
            'machine_id' => $request['machine_id'],
            'supplie_id' => $request['supplie_id'],
            'date' => $request['date'],
            'notes' => $request['notes'],
            'type' => 'machine_supplie',
        ]);



        if ($machineSupplie) {
            $new_track->quantity = $supplie->used;
            if ($trackingMachineSupplie) {
                $new_track->init_quantity = $trackingMachineSupplie->init_quantity;
                $new_track->last_quantity = $trackingMachineSupplie->last_quantity + $new_track->quantity;
            } else {
                $last_track = TrackingMachineSupplies::where('machine_id', $request['machine_id'])->orderBy('date', 'DESC')->where('id', '!=', $new_track->id)->where('supplie_id', $request['supplie_id'])->latest()->first();
                if ($last_track) {
                    $new_track->init_quantity = $last_track->last_quantity;
                } else {
                    $new_track->init_quantity = $machineSupplie->used;
                }
                $new_track->last_quantity = $new_track->init_quantity + $new_track->quantity;
            }
            $machineSupplie->increment('used', ($supplie->used));
            $machineSupplie->increment('quantity', $request['quantity']);
            $machineSupplie->date = $request->date;
            $machineSupplie->notes = $request->notes;
            $machineSupplie->transfer_quantity = $request->quantity;
            $machineSupplie->save();
            $new_track->save();
        } else {

            $machineSupplie = MachineSupplie::create($request_data);
            $new_track->quantity = $supplie->used;
            if ($trackingMachineSupplie) {
                $new_track->init_quantity = $trackingMachineSupplie->init_quantity;
                $new_track->last_quantity = $trackingMachineSupplie->last_quantity + $new_track->quantity;
            } else {
                $last_track = TrackingMachineSupplies::where('machine_id', $request['machine_id'])->orderBy('date', 'DESC')->where('id', '!=', $new_track->id)->where('supplie_id', $request['supplie_id'])->latest()->first();
                if ($last_track) {
                    $new_track->init_quantity = $last_track->last_quantity;
                } else {
                    $new_track->init_quantity = $machineSupplie->used;
                }
                $new_track->last_quantity = $new_track->init_quantity + $new_track->quantity;
            }
            $new_track->save();
        }

        $supplie->decrement('quantity', $request->quantity);
        $supplie->save();


        DB::commit();
        session()->flash('success', __('site.added_successfully'));
        return redirect()->route('dashboard.machine_supplies.index');
    }

    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'machine_id' => 'required',
    //         'supplie_id' => 'required',
    //         'quantity' => 'required',
    //         'date' => 'required',
    //         'notes',
    //     ]);

    //     $request_data = $request->all();

    //     DB::beginTransaction();
    //     $supplie = Supplies::where('id', $request->supplie_id)->first();
    //     $request_data['date'] = ($request['date']);
    //     $request_data['notes'] = ($request['notes']);
    //     $request_data['transfer_quantity'] = ($request['quantity']);
    //     $request_data['used'] = ($supplie->used * $request['quantity']);
    //     // $request_data['used'] = ($supplie->used * $request['quantity']);


    //     $machineSupplie = MachineSupplie::where('machine_id', $request['machine_id'])->where('supplie_id', $request['supplie_id'])->first();

    //     $trackingMachineSupplie = TrackingMachineSupplies::where('date', $request['date'])
    //         ->where('supplie_id', $request['supplie_id'])
    //         ->where('machine_id', $request['machine_id'])
    //         ->latest()->first();

    //     $new_track = TrackingMachineSupplies::create([
    //         'machine_id' => $request['machine_id'],
    //         'supplie_id' => $request['supplie_id'],
    //         'date' => $request['date'],
    //         'notes' => $request['notes'],
    //         'type' => 'machine_supplie',
    //     ]);



    //     if ($machineSupplie) {
    //         $new_track->quantity = $supplie->used * $request['quantity'];
    //         if ($trackingMachineSupplie) {
    //             $new_track->init_quantity = $trackingMachineSupplie->init_quantity;
    //             $new_track->last_quantity = $trackingMachineSupplie->last_quantity + $new_track->quantity;
    //         } else {
    //             $last_track = TrackingMachineSupplies::where('machine_id', $request['machine_id'])->orderBy('date', 'DESC')->where('id', '!=', $new_track->id)->where('supplie_id', $request['supplie_id'])->latest()->first();
    //             if ($last_track) {
    //                 $new_track->init_quantity = $last_track->last_quantity;
    //             } else {
    //                 $new_track->init_quantity = $machineSupplie->used;
    //             }
    //             $new_track->last_quantity = $new_track->init_quantity + $new_track->quantity;
    //         }
    //         $machineSupplie->increment('used', ($supplie->used * $request['quantity']));
    //         $machineSupplie->date = $request->date;
    //         $machineSupplie->notes = $request->notes;
    //         $machineSupplie->transfer_quantity = $request->quantity;
    //         $machineSupplie->increment('quantity', $request['quantity']);
    //         $machineSupplie->save();


    //         $new_track->save();
    //     } else {

    //         $machineSupplie = MachineSupplie::create($request_data);
    //         $new_track->quantity = $supplie->used * $request['quantity'];
    //         if ($trackingMachineSupplie) {
    //             $new_track->init_quantity = $trackingMachineSupplie->init_quantity;
    //             $new_track->last_quantity = $trackingMachineSupplie->last_quantity + $new_track->quantity;
    //         } else {
    //             $last_track = TrackingMachineSupplies::where('machine_id', $request['machine_id'])->orderBy('date', 'DESC')->where('id', '!=', $new_track->id)->where('supplie_id', $request['supplie_id'])->latest()->first();
    //             if ($last_track) {
    //                 $new_track->init_quantity = $last_track->last_quantity;
    //             } else {
    //                 $new_track->init_quantity = $machineSupplie->used;
    //             }
    //             $new_track->last_quantity = $new_track->init_quantity + $new_track->quantity;
    //         }
    //         $new_track->save();
    //     }

    //     $supplie->decrement('quantity', $request->quantity);
    //     $supplie->save();


    //     DB::commit();
    //     session()->flash('success', __('site.added_successfully'));
    //     return redirect()->route('dashboard.machine_supplies.index');
    // }
     
    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'machine_id' => 'required',
    //         'supplie_id' => 'required',
    //         'quantity' => 'required',
    //         'date' => 'required',
    //         'notes',
    //     ]);

    //     $request_data = $request->all();

    //     DB::beginTransaction();
    //     $supplie = Supplies::where('id', $request->supplie_id)->first();
    //     $request_data['used'] = ($supplie->used * $request['quantity']);


    //     $machineSupplie = MachineSupplie::where('machine_id', $request['machine_id'])->where('supplie_id', $request['supplie_id'])->first();

    //     $trackingMachineSupplie = TrackingMachineSupplies::where('date', $request['date'])
    //         ->where('supplie_id', $request['supplie_id'])
    //         ->where('machine_id', $request['machine_id'])
    //         ->latest()->first();

    //     $new_track = TrackingMachineSupplies::create([
    //         'machine_id' => $request['machine_id'],
    //         'supplie_id' => $request['supplie_id'],
    //         'date' => $request['date'],
    //         'notes' => $request['notes'],
    //         'type' => 'machine_supplie',
    //     ]);



    //     if ($machineSupplie) {
    //         $new_track->quantity = $supplie->used * $request['quantity'];
    //         if ($trackingMachineSupplie) {
    //             $new_track->init_quantity = $trackingMachineSupplie->init_quantity;
    //             $new_track->last_quantity = $trackingMachineSupplie->last_quantity + $new_track->quantity;
    //         } else {
    //             $last_track = TrackingMachineSupplies::where('machine_id', $request['machine_id'])->orderBy('date', 'DESC')->where('id', '!=', $new_track->id)->where('supplie_id', $request['supplie_id'])->latest()->first();
    //             if ($last_track) {
    //                 $new_track->init_quantity = $last_track->last_quantity;
    //             } else {
    //                 $new_track->init_quantity = $machineSupplie->used;
    //             }
    //             $new_track->last_quantity = $new_track->init_quantity + $new_track->quantity;
    //         }
    //         $machineSupplie->increment('used', ($supplie->used * $request['quantity']));
    //         $machineSupplie->increment('quantity', $request['quantity']);
    //         $machineSupplie->save();


    //         $new_track->save();
    //     } else {

    //         $machineSupplie = MachineSupplie::create($request_data);
    //         $new_track->quantity = $supplie->used * $request['quantity'];
    //         if ($trackingMachineSupplie) {
    //             $new_track->init_quantity = $trackingMachineSupplie->init_quantity;
    //             $new_track->last_quantity = $trackingMachineSupplie->last_quantity + $new_track->quantity;
    //         } else {
    //             $last_track = TrackingMachineSupplies::where('machine_id', $request['machine_id'])->orderBy('date', 'DESC')->where('id', '!=', $new_track->id)->where('supplie_id', $request['supplie_id'])->latest()->first();
    //             if ($last_track) {
    //                 $new_track->init_quantity = $last_track->last_quantity;
    //             } else {
    //                 $new_track->init_quantity = $machineSupplie->used;
    //             }
    //             $new_track->last_quantity = $new_track->init_quantity + $new_track->quantity;
    //         }
    //         $new_track->save();
    //     }

    //     $supplie->decrement('quantity', $request->quantity);
    //     $supplie->save();


    //     DB::commit();
    //     session()->flash('success', __('site.added_successfully'));
    //     return redirect()->route('dashboard.machine_supplies.index');
    // }
     
    /**
     * Momaher
     */
    
    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'machine_id' => 'required',
    //         'supplie_id' => 'required',
    //         'quantity' => 'required',
    //         'date' => 'required',
    //         'notes',
    //     ]);

    //     $request_data = $request->all();

    //     $supplie = Supplies::where('id', $request->supplie_id)->first();
    //     $request_data['used'] = $supplie->used;


    //     $machineSupplie = MachineSupplie::where('machine_id', $request['machine_id'])->where('supplie_id', $request['supplie_id'])->first();
    //     if ($machineSupplie) {
    //         $machineSupplie->increment('used', $supplie->used);
    //         $machineSupplie->increment('quantity', $request['quantity']);
    //         $machineSupplie->save();
    //     } else {
    //         $machineSupplie = MachineSupplie::create($request_data);
    //     }


    //     $supplie->decrement('quantity', $request->quantity);
    //     $supplie->save();

    //     session()->flash('success', __('site.added_successfully'));
    //     return redirect()->route('dashboard.machine_supplies.index');
    // }

    // public function store(Request $request)
    // {
    //     $request->validate([
    //         'machine_id' => 'required',
    //         'supplie_id' => 'required',
    //         'quantity' => 'required',
    //         'date' => 'required',
    //         'notes' ,
    //     ]);

    //     $request_data = $request->all();

    //     $supplie = Supplies::where('id', $request->supplie_id)->first();
    //     $request_data['used'] = $supplie->used;

    //     $machineSupplie = MachineSupplie::create($request_data);

    //     $supplie->decrement('quantity', $request->quantity);
    //     $supplie->save();

    //     session()->flash('success', __('site.added_successfully'));
    //     return redirect()->route('dashboard.machine_supplies.index');
    // }
    


    public function edit($machineSupplie)
    {
        $machineSupplie = MachineSupplie::find($machineSupplie);

        $machines = Machines::get();
        $supplies = Supplies::get();
        $machineTypes = MachineTypes::latest()->get();

        return view('dashboard.machine_supplies.edit', compact('machineSupplie','machineTypes' , 'machines' , 'supplies'));
    }

    public function update(Request $request, $machineSupplie)
    {
        $request->validate([
            'machine_id' => 'required',
            'supplie_id' => 'required',
            'quantity' => 'required',
            'date' => 'required',
            'used' => 'required',
            'notes' ,
        ]);
        $machineSupplie = MachineSupplie::find($machineSupplie);

        $request_data = $request->all();

        $machineSupplie->update($request_data);

        session()->flash('success', __('site.updated_successfully'));
        return redirect()->route('dashboard.machine_supplies.index');
    }


    public function destroy($machineSupplie)
    {
        $machineSupplie = MachineSupplie::find($machineSupplie);
        $machineSupplie->delete();
        session()->flash('success', __('site.deleted_successfully'));
        return redirect()->route('dashboard.machine_supplies.index');

    }

    public function getSuppliesQuantity(Request $request)
    {
        if (!$request->supply_id) {
            $html = '<option value="">'.trans('site.items').'</option>';
        } else {
            $html = '';
            $placeholder = '';
            $supplie = Supplies::where('id', $request->supply_id)
                                                ->where('used', ">" ,0)
                                                ->first();

            // $placeholder .= 'الكمية المتاحة= '. $supplie->quantity.' ';
            $supplyQuntity = $supplie->quantity;

        }

        return response()->json(['quantity' => $supplyQuntity]);
    }
}
