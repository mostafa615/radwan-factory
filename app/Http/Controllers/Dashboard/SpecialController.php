<?php

namespace App\Http\Controllers\Dashboard;

use App\Department;
use App\Student;
use App\Admin;
use App\Reposite;
use App\Special;
use App\Store;
use App\MachineTypes;
use App\Group;
use App\Quantity;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables as FacadesDataTables;
use Storage;

class SpecialController extends Controller
{

    public function __construct(){
        $this->middleware(['ability:admin,reade_specials'])->only('index');
        $this->middleware(['ability:admin,create_specials'])->only('create');
        $this->middleware(['ability:admin,update_specials'])->only('edit');
        $this->middleware(['ability:admin,delete_specials'])->only('destroy');

    }


    public function getData(Request $request) {
        $query = Special::query();
        $query->where('is_special', 1);


        return FacadesDataTables::eloquent($query->latest())
                        ->addColumn('action', function(Special $special) {
                            $type = "action";
                            return view("dashboard.specials.action", compact("special", "type"));
                        })
                        ->editColumn('group_id', function(Special $special){
                            return optional($special->Group)->name;
                        })
                        ->addColumn('quantity', function(Special $special){
                            $allQuantity=0;
                            $quantitis = Quantity::where('ownerable_type','App\Models\Store')
                                                ->where('item_id', $special->id)
                                                ->get();
                            //dd($quantitis);

                            // $itemQuantities = $special->quantities->where('ownerable_type','App\Models\Store');
                            foreach($quantitis as $quantity) {
                                $allQuantity += $quantity->quantity;
                            }
                            return $allQuantity;
                        })
                        ->rawColumns(['action', 'quantity'])
                        ->toJson();
    }

    public function index(Request $request)
    {
        $query = Special::query();
        $query->where('is_special', 1);

        $special = $query->with(['Group'])->get();

        return view('dashboard.specials.index', compact('special'));
    }

    public function create()
    {
        $groups = Group::where('id', 62)->get();

        return view('dashboard.specials.create' , compact('groups'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:items,name',
            'price' => 'required',
            'length' => 'required',
            'width' => 'required',
            'quantity' => 'required',
            'group_id' => 'required',
            'notes' ,
        ], [
            'name.unique' => 'الاسم مكود مسبقا'
        ]);
        
        $request_data = $request->all();
        $request_data['is_special'] = 1;

        $Special = Special::create($request_data);

        $Special->code = $Special->id;
        $Special->save();

        $stores=Store::get();
        foreach ($stores as $store){
            Quantity::create([
                'ownerable_id' => $store->id,
                'ownerable_type' => 'App\Models\Store',
                'item_id' => $Special->id,
                'quantity' => $request->quantity,
                'init' => $request->quantity
            ]);
        }

        session()->flash('success', __('site.added_successfully'));
        return redirect()->route('dashboard.specials.index');
    }

    public function edit($special)
    {
        $special = Special::find($special);

        $groups = Group::where('id', 62)->get();

        return view('dashboard.specials.edit', compact('special' , 'groups'));
    }

    public function update(Request $request, $special)
    {
        $special = Special::find($special);
        
        $request->validate([
            'name' => 'required|unique:items,name,'.$special->id,
            'price' => 'required',
            'group_id' => 'required',
            'notes' ,
        ], [
            'name.unique' => 'الاسم مكود مسبقا'
        ]);
        
        $request_data = $request->all();

        $special->update($request_data);

        session()->flash('success', __('site.updated_successfully'));
        return redirect()->route('dashboard.specials.index');
    }


    public function destroy($special)
    {
        $special = Special::find($special);
        $special->delete();
        session()->flash('success', __('site.deleted_successfully'));
        return redirect()->route('dashboard.specials.index');

    }
    
    public function auto_complete_first(Request $request)
    {
        $results = [];
        if($request->name) {
            $results = Special::where('name', 'LIKE', '%' . $request->name . '%')
                ->where('is_special', 1)
                ->pluck('name');
        }

        return response()->json($results);
    }
}
