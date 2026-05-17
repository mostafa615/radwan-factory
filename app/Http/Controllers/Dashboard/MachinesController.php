<?php

namespace App\Http\Controllers\Dashboard;

use App\Department;
use App\Student;
use App\Admin;
use App\Reposite;
use App\Machines;
use App\MachineTypes;
use App\Branch;
use App\Store;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables as FacadesDataTables;
use Storage;
use Pusher\Pusher;

class MachinesController extends Controller
{

    public function __construct(){
        $this->middleware(['ability:admin,read_machines'])->only('index');
        $this->middleware(['ability:admin,create_machines'])->only('create');
        $this->middleware(['ability:admin,update_machines'])->only('edit');
        $this->middleware(['ability:admin,delete_machines'])->only('destroy');

    }

    public function getData(Request $request) {
        $query = Machines::query();


        return FacadesDataTables::eloquent($query->latest())
                        ->addColumn('action', function(Machines $machines) {
                            $type = "action";
                            return view("dashboard.machines.action", compact("machines", "type"));
                        })
                        ->editColumn('type', function(Machines $machines){
                            return optional($machines->MachineType)->name;

                        })
                        ->editColumn('store_id', function(Machines $machines){
                            return optional($machines->Store)->name;

                        })
                        ->rawColumns(['action'])
                        ->toJson();
    }

    public function index(Request $request)
    {
        $query = Machines::query();

        $machines = $query->with(['MachineType'])->get();

        return view('dashboard.machines.index', compact('machines'));
    }

    public function create()
    {
        $machine_types = MachineTypes::get();
        $branch = Branch::get();
        $stores = Store::get();

        return view('dashboard.machines.create' , compact('machine_types', 'stores' , 'branch'));
    }

    public function store(Request $request)
    {

        $request->validate([
            'name' => 'required',
            'type' => 'required',
            'store_id' => 'required',
            'description' ,
        ]);

        $request_data = $request->all();

        $Machines = Machines::create($request_data);
        $store = DB::table('stores')
                    ->select('id','name')
                    ->where('id', $request->store_id)
                    ->first();
                    
        $this->push_notification(['user_id' => auth()->user()->id,'url'=>url('machines')]);
        // $this->push_notification(['user_id' => $store->user_id,'url'=>url('machines')]);


        session()->flash('success', __('site.added_successfully'));
        return redirect()->route('dashboard.machines.index');
    }

    public function edit($machines)
    {
        $machines = Machines::find($machines);
        $stores = Store::get();
        $machine_types = MachineTypes::get();
        $branch = Branch::get();

        return view('dashboard.machines.edit', compact('machines', 'stores' , 'machine_types' , 'branch'));
    }

    public function update(Request $request, $machines)
    {
        $request->validate([
            'name' => 'required',
            'type' => 'required',
            'store_id' => 'required',
            'description' ,
        ]);
        $machines = Machines::find($machines);

        $request_data = $request->all();

        $machines->update($request_data);

        session()->flash('success', __('site.updated_successfully'));
        return redirect()->route('dashboard.machines.index');
    }


    public function destroy($machines)
    {
        $machines = Machines::find($machines);
        $machines->delete();
        session()->flash('success', __('site.deleted_successfully'));
        return redirect()->route('dashboard.machines.index');

    }

    public function getMachinesByType(Request $request)
    {
        if (!$request->type) {
            $html = '<option value="">الالات</option>';
        } else {
            $html = '<option value="">الألات</option>';
            $machines = Machines::where('type', $request->type)->get();
            // dd($machines);
            foreach ($machines as $machine) {
                $html .= '<option value="'.$machine->id.'">'.$machine->name.'</option>';
            }
        }

        return response()->json(['html' => $html]);
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
