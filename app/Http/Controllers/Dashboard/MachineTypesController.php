<?php

namespace App\Http\Controllers\Dashboard;

use App\Department;
use App\Student;
use App\Admin;
use App\Reposite;
use App\MachineTypes;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables as FacadesDataTables;
use Storage;
use Pusher\Pusher;

class MachineTypesController extends Controller
{
    public function __construct(){
        $this->middleware(['ability:admin,read_machine_types'])->only('index');
        $this->middleware(['ability:admin,create_machine_types'])->only('create');
        $this->middleware(['ability:admin,update_machine_types'])->only('edit');
        $this->middleware(['ability:admin,delete_machine_types'])->only('destroy');

    }


    public function getData(Request $request) {
        $query = MachineTypes::query();




        return FacadesDataTables::eloquent($query->latest())
                        ->addColumn('action', function(MachineTypes $machinetype) {
                            $type = "action";
                            return view("dashboard.machine_types.action", compact("machinetype", "type"));
                        })
                        ->rawColumns(['action'])
                        ->toJson();
    }

    public function index(Request $request)
    {


        $query = MachineTypes::query();


        $machinetype = $query->get();

        return view('dashboard.machine_types.index', compact('machinetype'));
    }

    public function create()
    {
        return view('dashboard.machine_types.create');
    }

    public function store(Request $request)
    {

        $request->validate([
            'name' => 'required',
            'description' ,
        ]);

        $request_data = $request->all();

        $MachineTypes = MachineTypes::create($request_data);


        session()->flash('success', __('site.added_successfully'));
        $this->push_notification(['user_id' => auth()->user()->id,'url'=>url('machine_types')]);
        return redirect()->route('dashboard.machine_types.index');
    }

    public function edit($machinetype)
    {
        $machinetype = MachineTypes::find($machinetype);


        return view('dashboard.machine_types.edit', compact('machinetype'));
    }

    public function update(Request $request, $machinetype)
    {
        $request->validate([
            'name' => 'required',
            'description'
        ]);
        $machinetype = MachineTypes::find($machinetype);

        $request_data = $request->all();

        $machinetype->update($request_data);

        session()->flash('success', __('site.updated_successfully'));
        return redirect()->route('dashboard.machine_types.index');
    }


    public function destroy($machinetype)
    {
        $machinetype = MachineTypes::find($machinetype);
        $machinetype->delete();
        session()->flash('success', __('site.deleted_successfully'));
        return redirect()->route('dashboard.machine_types.index');

    }
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
}
