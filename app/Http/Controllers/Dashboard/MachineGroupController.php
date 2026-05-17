<?php

namespace App\Http\Controllers\Dashboard;

use App\Department;
use App\Student;
use App\Admin;
use App\Reposite;
use App\Machines;
use App\Quantity;
use App\MachineGroup;
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

class MachineGroupController extends Controller
{
    public function __construct(){
        $this->middleware(['ability:admin,reade_machine_groups'])->only('index');
        $this->middleware(['ability:admin,create_machine_groups'])->only('create');
        $this->middleware(['ability:admin,update_machine_groups'])->only('edit');
        $this->middleware(['ability:admin,delete_machine_groups'])->only('destroy');

    }

    public function getData(Request $request) {
        $query = MachineGroup::query();


        return FacadesDataTables::eloquent($query->latest())
                        ->addColumn('action', function(MachineGroup $machineGroup) {
                            $type = "action";
                            return view("dashboard.machine_groups.action", compact("machineGroup", "type"));
                        })
                        ->editColumn('machine_id', function(MachineGroup $machineGroup){
                            return optional($machineGroup->Machine)->name;
                        })
                        ->editColumn('group_id', function(MachineGroup $machineGroup){
                            // return optional($machineGroup->Group)->name;
                            $groupIds = explode(',', $machineGroup->group_id);
                            $groupsNames = Group::whereIn('id', $groupIds)->pluck('name')->toArray();
                            return $groupsNames;
                        })
                        ->rawColumns(['action'])
                        ->toJson();
    }

    public function index(Request $request)
    {
        $query = MachineGroup::query();

        $query->with(['Machine', 'Group'])->latest()->get();

        return view('dashboard.machine_groups.index');
    }

    public function create()
    {
        $machines = Machines::get();
        $machineTypes = MachineTypes::latest()->get();
        $groups = Group::latest()->get();

        return view('dashboard.machine_groups.create' , compact('machines', 'groups' ,'machineTypes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'machine_id' => 'required',
            'group_id' => 'required',
            'type' => 'required',
            'notes' ,
        ]);

        $request_data = $request->all();

        $groups = $request->input('group_id');
        $request_data['group_id'] = implode(',', $groups);

        $machineGroup = MachineGroup::create($request_data);


        session()->flash('success', __('site.added_successfully'));
        return redirect()->route('dashboard.machine_groups.index');
    }

    public function edit($machineGroup)
    {
        $machineGroup = MachineGroup::find($machineGroup);
        $machineTypes = MachineTypes::latest()->get();
        $machines = Machines::get();
        $groups = Group::latest()->get();

        return view('dashboard.machine_groups.edit', compact('machineGroup', 'groups', 'machineTypes' , 'machines'));
    }

    public function update(Request $request, $machineGroup)
    {
        $request->validate([
            'machine_id' => 'required',
            'group_id' => 'required',
            'type' => 'required',
            'notes' ,
        ]);
        $machineGroup = MachineGroup::find($machineGroup);

        $request_data = $request->all();

        $groups = $request->input('group_id');
        $request_data['group_id'] = implode(',', $groups);

        $machineGroup->update($request_data);

        session()->flash('success', __('site.updated_successfully'));
        return redirect()->route('dashboard.machine_groups.index');
    }


    public function destroy($machineGroup)
    {
        $machineGroup = MachineGroup::find($machineGroup);
        $machineGroup->delete();
        session()->flash('success', __('site.deleted_successfully'));
        return redirect()->route('dashboard.machine_groups.index');

    }


}
