<?php

namespace App\Http\Controllers\Dashboard;

use App\Department;
use App\Student;
use App\Admin;
use App\Reposite;
use App\Machines;
use App\Quantity;
use App\MachineItem;
use App\Item;
use App\Group;
use App\MachineTypes;
use App\Branch;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables as FacadesDataTables;
use Storage;

class MachineItemController extends Controller
{


    public function getData(Request $request) {
        $query = MachineItem::query();


        return FacadesDataTables::eloquent($query->latest())
                        ->addColumn('action', function(MachineItem $machineItem) {
                            $type = "action";
                            return view("dashboard.machine_items.action", compact("machineItem", "type"));
                        })
                        ->editColumn('machine_id', function(MachineItem $machineItem){
                            return optional($machineItem->Machine)->name;
                        })
                        ->editColumn('item_id', function(MachineItem $machineItem){
                            return optional($machineItem->Item)->name;
                        })
                        ->rawColumns(['action'])
                        ->toJson();
    }

    public function index(Request $request)
    {
        $query = MachineItem::query();

        $query->with(['Machine', 'Item'])->latest()->get();

        return view('dashboard.machine_items.index');
    }

    public function create()
    {
        $machines = Machines::get();
        $machineTypes = MachineTypes::latest()->get();
        $items = Item::get();
        $groups = Group::latest()->get();

        return view('dashboard.machine_items.create' , compact('machines', 'groups' ,'machineTypes','items'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'machine_id' => 'required',
            'item_id' => 'required',
            'quantity' => 'required',
            'date' => 'required',
            'notes' ,
        ]);

        $request_data = $request->all();

        $machineItem = MachineItem::create($request_data);

        $machine = Machines::where('id',$request->machine_id)->first();

        $store = DB::table('stores')
                    ->select('id','name')
                    ->where('id', $machine->store_id)
                    ->first();

        $quantity = Quantity::where('ownerable_type', 'App\Models\Store')
                            ->where('ownerable_id', $store->id)
                            ->where('item_id', $request->item_id)
                            ->first();
        $quantity->decrement('quantity', $request->quantity);
        $quantity->save();

        session()->flash('success', __('site.added_successfully'));
        return redirect()->route('dashboard.machine_items.index');
    }

    public function edit($machineItem)
    {
        $machineItem = MachineItem::find($machineItem);
        $machineTypes = MachineTypes::latest()->get();
        $machines = Machines::get();
        $items = Item::get();
        $groups = Group::latest()->get();

        return view('dashboard.machine_items.edit', compact('machineItem', 'groups', 'machineTypes' , 'machines' , 'items'));
    }

    public function update(Request $request, $machineItem)
    {
        $request->validate([
            'machine_id' => 'required',
            'item_id' => 'required',
            'quantity' => 'required',
            'date' => 'required',
            'notes' ,
        ]);
        $machineItem = MachineItem::find($machineItem);

        $request_data = $request->all();

        $machineItem->update($request_data);

        session()->flash('success', __('site.updated_successfully'));
        return redirect()->route('dashboard.machine_items.index');
    }


    public function destroy($machineItem)
    {
        $machineItem = MachineItem::find($machineItem);
        $machineItem->delete();
        session()->flash('success', __('site.deleted_successfully'));
        return redirect()->route('dashboard.machine_items.index');

    }

    public function getItemQuantity(Request $request)
    {
        if (!$request->item_id) {
            $html = '<option value="">'.trans('site.items').'</option>';
        } else {
            $html = '';

            $machine = Machines::where('id',$request->machine_id)->first();

            $store = DB::table('stores')
                    ->select('id','name')
                    ->where('id', $machine->store_id)
                    ->first();

            $itemQuantity = Quantity::where('ownerable_type', 'App\Models\Store')
                                ->where('ownerable_id', $store->id)
                                ->where('item_id', $request->item_id)
                                ->first();

            if(!$itemQuantity){
                $quantity = 'لايوجد كمية في هذا المخزن';
            }else{
                $quantity = $itemQuantity->quantity;
            }

        }

        return response()->json(['quantity' => $quantity]);
    }
}
