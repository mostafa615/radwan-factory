<?php

namespace App\Http\Controllers\Dashboard;

use App\StdHistory;
use App\Student;
use App\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Storage;
use Yajra\DataTables\Facades\DataTables as FacadesDataTables;

class StdHistoryController extends Controller
{
    /*public function __construct(){
        $this->middleware(['permission:read_parent_stds'])->only('index');
        $this->middleware(['permission:create_parent_stds'])->only('create');
        $this->middleware(['permission:update_parent_stds'])->only('edit');
        $this->middleware(['permission:delete_parent_stds'])->only('destroy');

    }*/

    public function getData(Request $request) {
        $query = StdHistory::query();
        //$course = Subject::find(request()->course_id);

        if(auth()->user()->type == 'admin' || auth()->user()->type == 'super_admin')
            $query->latest()->get();

        if(auth()->user()->type == 'student' )
            $query->where('student_id', auth()->user()->fid)->get();

        if(auth()->user()->type == 'parent' )
            $query->where('student_id', auth()->user()->toParent()->student->id)->get();

        return FacadesDataTables::eloquent($query->latest())
                        ->addColumn('action', function(StdHistory $stdHistory) {
                            $type = "action";
                            return view("dashboard.student_histories.action", compact("stdHistory", "type"));
                        })
                        ->editColumn('student_id', function(StdHistory $stdHistory) {
                            return optional($stdHistory->student)->name;
                        })
                        ->editColumn('file', function(StdHistory $stdHistory) {
                            $type = "file";
                            return view("dashboard.student_histories.action", compact("stdHistory", "type"));
                        })
                        ->rawColumns(['action'])
                        ->toJson();
    }

    public function index(Request $request)
    {

        $query = StdHistory::query();
        if ($request->search)
            $query->where('name', 'like', '%'. $request->search . '%');

        if ($request->student_id > 0)
            $query->where('student_id', $request->student_id);


        $stdHistorys = $query->latest()->get();

        return view('dashboard.student_histories.index', compact(['stdHistorys']));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $students = Student::all();
        return view('dashboard.student_histories.create', compact(['students']));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'student_id' => 'required',
            'description' => 'required',
        ]);
        $request_data = $request->except(['file']);


        if($request->hasFile('file')){
            /*
                $file = $request->file('file');
                $filename=time().'.'.$file->getClientOriginalExtension();

                $request_data['file'] = $filename;

                $destinationPath = public_path('uploads/stdHisory');
                $file->move($destinationPath,$filename);
            */
            $driveName = $request->file('file')->store('1uUopLsJPGDi5xocwjMrdUR_CYXZPzuUQ', 'google');
            $url = Storage::disk('google')->url($driveName);

            $file = $request->file('file');
            $filename=time().'.'.$file->getClientOriginalExtension();
            $request_data['file'] = $url;
        }

        $stdHistory = StdHistory::create($request_data);


        session()->flash('success', __('site.added_successfully'));
        return redirect()->route('dashboard.student_histories.index');
    }


    public function edit(StdHistory $student_history)
    {
        $students = Student::all();
        return view('dashboard.student_histories.edit', compact('student_history','students'));
    }


    public function update(Request $request, StdHistory $student_history)
    {
        $request->validate([
            'student_id' => 'required',
            'description' => 'required',
        ]);
        $request_data = $request->except(['file']);


        if($request->hasFile('file')){
            /*
                $file = $request->file('file');
                $filename=time().'.'.$file->getClientOriginalExtension();

                $request_data['file'] = $filename;

                $destinationPath = public_path('uploads/stdHisory');
                $file->move($destinationPath,$filename);
            */
            $driveName = $request->file('file')->store('1uUopLsJPGDi5xocwjMrdUR_CYXZPzuUQ', 'google');
            $url = Storage::disk('google')->url($driveName);

            $file = $request->file('file');
            $filename=time().'.'.$file->getClientOriginalExtension();
            $request_data['file'] = $url;
        }
        $student_history->update($request_data);


        session()->flash('success', __('site.updated_successfully'));
        return redirect()->route('dashboard.student_histories.index');
    }

    public function destroy(StdHistory $student_history)
    {

        $student_history->delete();
        session()->flash('success', __('site.deleted_successfully'));
        return redirect()->route('dashboard.student_histories.index');

    }
}
