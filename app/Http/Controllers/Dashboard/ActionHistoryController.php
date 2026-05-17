<?php

namespace App\Http\Controllers\Dashboard;

use App\Department;
use App\Admin;
use App\Reposite;
use App\SupplieTypes;
use App\Supplies;
use App\ActionHistory;
use App\ActionHistoryDetail;
use App\ActionHistoryResult;
use App\Machines;
use App\MachineItem;
use App\MachineGroup;
use App\MachineTypes;
use App\MachineSupplie;
use App\Item;
use App\Quantity;
use App\Group;
use App\Employee;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables as FacadesDataTables;
use Storage;
use Pusher\Pusher;
use Illuminate\Support\Arr;

class ActionHistoryController extends Controller
{

    public function getData(Request $request) {
        $query = ActionHistory::query();

        if($request->user_id > 0 )
            $query->where('user_id', $request->user_id);

        if(isset($request->action) && !empty($request->action)){
            $query->where('action', $request->action);
        }

        if(isset($request->date_from) && isset($request->date_to)){
            $query->whereDate('date', '>=', $request->date_from)
                ->whereDate('date', '<=', $request->date_to);
        }else{
            $query->whereDate('date', Carbon::now()->format('Y-m-d'));
        }


        return FacadesDataTables::eloquent($query->with(['user'])->latest('id'))

                        ->editColumn('user_id',function(ActionHistory $actionHistory){
                            return optional($actionHistory->user)->name;
                        })
                        ->editColumn('action',function(ActionHistory $actionHistory){
                            return __('site.'.$actionHistory->action);
                        })
                        ->editColumn('system',function(ActionHistory $actionHistory){
                            return __('site.'.$actionHistory->system);
                        })
                        ->editColumn('model_type',function(ActionHistory $actionHistory){
                            return __('site.'.$actionHistory->model_type);
                        })
                        ->rawColumns(['user_id'])
                        ->toJson();
    }


    public function index(Request $request)
    {
        $query = ActionHistory::query();

        $actionHistories = $query->latest()->get();

        // $users  = User::where('id', '<>', 1)->latest()->get();
        $users  = User::latest()->get();

        return view('dashboard.action_histories.index', compact('actionHistories', 'users'));
    }

}
