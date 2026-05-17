<?php

namespace App\Http\Controllers\Dashboard;

use App\Department;
use App\Student;
use App\Admin;
use App\Reposite;
use App\Damage;
use App\MachineTypes;
use App\Group;
use App\Special;
use App\Quantity;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use App\Store;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables as FacadesDataTables;
use Storage;

class DamageController extends Controller
{

    public function __construct(){
        $this->middleware(['ability:admin,reade_damages'])->only('index');
        $this->middleware(['ability:admin,create_damages'])->only('create');
        $this->middleware(['ability:admin,update_damages'])->only('edit');
        $this->middleware(['ability:admin,delete_damages'])->only('destroy');

    }

    public function getData(Request $request) {
        $query = Damage::query();
        $query->where('is_damage', 1);
        $query->with(['quantities' => function ($q) {
            $q->where('quantity', "!=", 0);
        }])->whereHas('quantities', function ($q) {
            $q->where('quantity', "!=", 0);
        });


        return FacadesDataTables::eloquent($query->latest())
                        ->addColumn('action', function(Damage $damage) {
                            $type = "action";
                            return view("dashboard.damages.action", compact("damage", "type"));
                        })
                        ->addColumn('quantity', function(Damage $damage){
                            $allQuantity=0;
                            $quantitis = Quantity::where('ownerable_type','App\Models\Store')
                                                ->where('item_id', $damage->id)
                                                ->get();
                            //dd($quantitis);
                            // $itemQuantities = $damage->quantities->where('ownerable_type','App\Models\Store');
                            foreach($quantitis as $quantity) {
                                $allQuantity += $quantity->quantity;
                            }
                            return $allQuantity;
                        })
                        ->editColumn('group_id', function(Damage $damage){
                            return optional($damage->Group)->name;
                        })
                        ->rawColumns(['action','quantity'])
                        ->toJson();
    }

    public function index(Request $request)
    {

        $query = Damage::query();

        $query->where('is_damage', 1);

        $damage = $query->where('is_damage', 1)->with(['Group'])->latest()->get();

        return view('dashboard.damages.index', compact('damage'));
    }

    public function create()
    {
        $groups = Group::where('id', 63)->get();

        return view('dashboard.damages.create' , compact('groups'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:items,name',
            'price' => 'required',
            'group_id' => 'required',
            'notes' ,
        ], [
            'name.unique' => 'الاسم مكود مسبقا'
        ]);
        
        $request_data = $request->all();
        $request_data['is_damage'] = 1;

        $Damage = Damage::create($request_data);

        $Damage->code = $Damage->id;
        $Damage->save();

        $stores=Store::get();
        foreach ($stores as $store){
            Quantity::create([
                'ownerable_id' => $store->id,
                'ownerable_type' => 'App\Models\Store',
                'item_id' => $Damage->id,
                'quantity' => 0
            ]);
        }

        session()->flash('success', __('site.added_successfully'));
        return redirect()->route('dashboard.damages.index');
    }

    public function edit($damage)
    {
        $damage = Damage::find($damage);

        $groups = Group::where('id', 63)->get();

        return view('dashboard.damages.edit', compact('damage' , 'groups'));
    }

    public function update(Request $request, $damage)
    {
        $damage = Damage::find($damage);
        
        $request->validate([
            'name' => 'required|unique:items,name,'.$damage->id,
            'price' => 'required',
            'group_id' => 'required',
            'notes' ,
        ], [
            'name.unique' => 'الاسم مكود مسبقا'
        ]);
        
        $request_data = $request->all();

        $damage->update($request_data);

        session()->flash('success', __('site.updated_successfully'));
        return redirect()->route('dashboard.damages.index');
    }


    public function destroy($damage)
    {
        $damage = Damage::find($damage);
        $damage->delete();
        session()->flash('success', __('site.deleted_successfully'));
        return redirect()->route('dashboard.damages.index');

    }
    
    public function auto_complete_first(Request $request)
    {
        $results = [];
        if($request->name) {
            $results = Damage::where('name', 'LIKE', '%' . $request->name . '%')
                ->where('is_damage', 1)
                ->pluck('name');
        }

        return response()->json($results);
    }
}
