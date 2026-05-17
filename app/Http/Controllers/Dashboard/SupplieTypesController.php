<?php

namespace App\Http\Controllers\Dashboard;

use App\Department;
use App\Student;
use App\Admin;
use App\Reposite;
use App\SupplieTypes;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables as FacadesDataTables;
use Storage;

class SupplieTypesController extends Controller
{
    public function __construct(){
        $this->middleware(['ability:admin,reade_supplie_types'])->only('index');
        $this->middleware(['ability:admin,create_supplie_types'])->only('create');
        $this->middleware(['ability:admin,update_supplie_types'])->only('edit');
        $this->middleware(['ability:admin,delete_supplie_types'])->only('destroy');

    }


    public function getData(Request $request) {
        $query = SupplieTypes::query();


        return FacadesDataTables::eloquent($query->latest())
                        ->addColumn('action', function(SupplieTypes $supplietype) {
                            $type = "action";
                            return view("dashboard.supplie_types.action", compact("supplietype", "type"));
                        })
                        ->rawColumns(['action'])
                        ->toJson();
    }

    public function index(Request $request)
    {


        $query = SupplieTypes::query();


        $supplietype = $query->get();

        return view('dashboard.supplie_types.index', compact('supplietype'));
    }

    public function create()
    {
        return view('dashboard.supplie_types.create');
    }

    public function store(Request $request)
    {

        $request->validate([
            'name' => 'required',
            'description' ,
        ]);

        $request_data = $request->all();

        $SupplieTypes = SupplieTypes::create($request_data);


        session()->flash('success', __('site.added_successfully'));
        return redirect()->route('dashboard.supplie_types.index');
    }

    public function edit($supplietype)
    {
        $supplietype = SupplieTypes::find($supplietype);


        return view('dashboard.supplie_types.edit', compact('supplietype'));
    }

    public function update(Request $request, $supplietype)
    {
        $request->validate([
            'name' => 'required',
            'description'
        ]);
        $supplietype = SupplieTypes::find($supplietype);

        $request_data = $request->all();

        $supplietype->update($request_data);

        session()->flash('success', __('site.updated_successfully'));
        return redirect()->route('dashboard.supplie_types.index');
    }


    public function destroy($supplietype)
    {
        $supplietype = SupplieTypes::find($supplietype);
        $supplietype->delete();
        session()->flash('success', __('site.deleted_successfully'));
        return redirect()->route('dashboard.supplie_types.index');

    }
}
