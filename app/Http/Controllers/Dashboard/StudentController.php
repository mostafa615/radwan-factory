<?php

namespace App\Http\Controllers\Dashboard;

use App\Department;
use App\Attachment;
use App\StudentAttachment;
use App\Exports\StudentsExport;
use App\Student;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Imports\StudentsImport;
use App\Level;
use App\Recommende;
use App\Government;
use App\Nationality;
use App\Currency;
use App\User;
use App\Installment;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables as FacadesDataTables;
use Storage;
class StudentController extends Controller
{
    public function __construct(){
        $this->middleware(['permission:read_students'])->only('index');
        $this->middleware(['permission:create_students'])->only('create');
        $this->middleware(['permission:update_students'])->only('edit');
        $this->middleware(['permission:delete_students'])->only('destroy');

    }

    public function importExportView()
    {
       return view('dashboard.students.index');
    }

    public function export()
    {
        return Excel::download(new StudentsExport, 'students.xlsx');
    }

    public function import()
    {
        Excel::import(new StudentsImport,request()->file('file'));

        return back();
    }

    public function attendView(){
        return vi-ew('auth.studentAttend');
    }
    public function leaveView(){
        return view('auth.studentLeave');
    }

    public function attendStore(Request $request){

        $validator = validator()->make($request->all(), [
            'email' => 'required',
        ], [
            "email.required" => __("national_id_required"),
        ]);

        $student = Student::where('national_id', $request->email)->first();

        DB::insert('insert into attend (std_name, std_national_id, std_level, std_depart, created_at) values (?, ?,?,?,?)',
                    [$student->name,
                    $student->national_id,
                     $student->level->name,
                     $student->department->name,
                     date("Y-m-d")
                    ]);

        notify()->success(trans('تم تسجيل الحضور'),"Success","topRight");
        //return redirect($redirect . "?status=1&msg=" . __('your complaint sent to admin'));
        return redirect()->back();
    }

    public function leaveStore(Request $request){

        $validator = validator()->make($request->all(), [
            'email' => 'required',
        ], [
            "email.required" => __("national_id_required"),
        ]);

        $student = Student::where('national_id', $request->email)->first();

        DB::insert('insert into std_leave (std_name, std_national_id, std_level, std_depart) values (?, ?,?,?)',
                    [$student->name,
                    $student->national_id,
                     $student->level->name,
                     $student->department->name,
                    ]);

        notify()->success(trans('تم تسجيل الانصراف'),"Success","topRight");
        //return redirect($redirect . "?status=1&msg=" . __('your complaint sent to admin'));
        return redirect()->back();
    }

    public function studentAttend(){
        return view('dashboard.students.attend');
    }
    public function studentLeave(){
        return view('dashboard.students.leave');
    }

    public function studentAttendGetData(Request $request){
        $query = DB::select('select * from attend ORDER BY id DESC');

        return FacadesDataTables::of($query)->toJson();
    }
    public function studentLeaveGetData(Request $request){
        $query = DB::select('select * from std_leave ORDER BY id DESC');

        return FacadesDataTables::of($query)->toJson();
    }



    public function getData(Request $request) {
        $query = Student::query();
        //$course = Subject::find(request()->course_id);
        if ($request->level_id > 0)
            $query->where('level_id', request()->level_id);
               
        if ($request->department_id > 0)
            $query->where('department_id', $request->department_id);
               
        if ($request->nationality_id > 0)
            $query->where('nationality_id', $request->nationality_id);
            
        if ($request->government_id > 0)
            $query->where('government_id', $request->government_id);
       
        if ($request->recommende_id > 0)
            $query->where('recommende_id', request()->recommende_id);
            
        // if ($request->military_status_id > 0)
        //     $query->where('military_status_id', request()->military_status_id);

        return FacadesDataTables::eloquent($query->latest())
                        ->addColumn('action', function(Student $student) {
                            $type = "action";
                            return view("dashboard.students.action", compact("student", "type"));
                        })
                        ->addColumn('file', function(Student $student) {
                            $type = "file";
                            return view("dashboard.students.action", compact("student", "type"));
                        })
                        ->addColumn('fees', function(Student $student) {
                            $type = "fees";
                            return view("dashboard.students.action", compact("student", "type"));
                        })
                        ->editColumn('level_id', function(Student $student) {
                            return optional($student->level)->name;
                        })
                         ->editColumn('government_id', function(Student $student) {
                            return optional($student->government)->name;
                        })
                         ->editColumn('nationality_id', function(Student $student) {
                            return optional($student->nationality)->name;
                        })
                        ->editColumn('department_id', function(Student $student) {
                            return optional($student->department)->name;
                        })
                        ->editColumn('recommende_id', function(Student $student) {
                            return optional($student->recommende)->name;
                        })
                        ->editColumn('military_status_id', function(Student $student) {
                            return optional($student->military_status)->name;
                        })
                        ->editColumn('active', function(Student $student) {
                            $type = "active";
                            return view("dashboard.students.action", compact("student", "type"));
                        })/*
                        ->editColumn('account_confirm', function(Student $student) {
                            $type = "account_confirm";
                            return view("dashboard.students.action", compact("student", "type"));
                        })*/
                        ->rawColumns(['action', 'active', 'account_confirm'])
                        ->toJson();
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $levels = Level::all();
        $recommendes = Recommende::all();
        $departments = Department::all();
        $governments = Government::all();
        $nationals = Nationality::all();

        /*$students = Student::when($request->search, function ($q) use ($request){
            return $q->where('name', 'like', '%'. $request->search . '%')
                    ->orWhere('code', 'like', '%'. $request->search . '%')
                    ->orWhere('level_id',$request->level_id)
                    ->orWhere('department_id', $request->department_id);
        })->get();*/

        $query = Student::query();
        if ($request->search)
            $query->where('name', 'like', '%'. $request->search . '%');

        if ($request->level_id > 0)
            $query->where('level_id', $request->lesson_id);

        if ($request->department_id > 0)
            $query->where('department_id', $request->department_id);

        if ($request->national_id > 0)
            $query->where('national_id', $request->national_id);

        if ($request->government_id > 0)
            $query->where('government_id', $request->government_id);

        $students = $query->get();

        return view('dashboard.students.index', compact('students','levels', 'departments','governments', 'recommendes', 'nationals'));
    }

    public function get_by_level(Request $request)
    {
        //abort_unless(\Gate::allows('city_access'), 401);

        if (!$request->level_id) {
            $html = '<option value="">'.trans('site.departments').'</option>';
        } else {
            $html = '';
            // $subjects = Subject::where('doc_id', $request->doc_id)->get();
            $departments = Department::where('level_id', $request->level_id)->get();
            foreach ($departments as $department) {
                $html .= '<option value="'.$department->id.'">'.$department->name.'</option>';
            }
        }

        return response()->json(['html' => $html]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $levels = Level::all();
        $recommendes = Recommende::all();
        $military_status = DB::table('military_status')->get();
        $departments = Department::all();
        $governments = Government::all();
        $nationalities = Nationality::all();
        $currencies = Currency::all();

        return view('dashboard.students.create', compact('levels', 'departments','military_status','governments','nationalities','recommendes','currencies'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        
       
        

        // dd($request->spl_start_date);
        //add doctor to user table
        $request->validate([
            'name' => 'required',
            // 'email' => 'required|unique:users',
            'username' => 'required|unique:users',
            'phone' => 'required|unique:users',
            'type'  =>  'student',
            'active' => 'required',

            'password' => 'required|confirmed',

            'level_id' => 'required',
            'department_id' => 'nullable',
            'code' => 'required||unique:students',
            'national_id' => 'required',
            // 'set_number' => 'required',
            'account_confirm' => 'required',
            //'permissions' => 'required|min:1',
            'installments' => 'required',


        ]);
        $request_data = $request->except(['password', 'password_confirmation', 'permissions']);
        $request_data['password'] = bcrypt($request->password);
        $request_data['type'] = 'student';
        //$request_data['fid']  = $student->id;

        if ($request_data['active'] == 'on' || $request_data['active'] == 1)
            $request_data['active'] = 1;
        else
        $request_data['active'] = 0;


        $user = User::create($request_data);
        $user->attachRole('student');
        //$user->syncPermissions($request->permissions);

        $request->validate([
            'name' => 'required',
            'level_id' => 'required',
            'department_id' => 'nullable',
            'code' => 'required||unique:students',
            // 'email' => 'required|unique:students',
            'username' => 'required|unique:students',
            'phone' => 'required|unique:students',
            'active' => 'required',
            'account_confirm' => 'required',
            'national_id' => 'required',
            // 'set_number' => 'required',
            'password' => 'required|confirmed',
            
            //'permissions' => 'required|min:1',

        ]);

        $request_data = $request->except(['password', 'password_confirmation', 'permissions']);
        $request_data['password'] = bcrypt($request->password);

        if ($request_data['active'] == 'on' || $request_data['active'] == 1)
            $request_data['active'] = 1;
        else
            $request_data['active'] = 0;
            
        $student = Student::create($request_data);
        $student->attachRole('student');
        $user->update(['fid'=>$student->id]);
        //$student->syncPermissions($request->permissions);

        if($request->hasFile('criminal_records')){

            $driveName = $request->file('criminal_records')->store('19YZc9HcWSQlaQG_BfMZVUXtY9zhEj6Qz', 'google');
            $url = Storage::disk('google')->url($driveName);

            $criminal_records = $request->file('criminal_records');
            $criminal_recordsname=time().'.'.$criminal_records->getClientOriginalExtension();

            $student->criminal_records = $url;
            $student->save();
            $attachment = Attachment::where('name','criminal_records')->first();

            if($attachment)
            $studentAttachment = StudentAttachment::create(['student_id' => $student->id,'attachment_id' => $attachment->id, 'path' => $url]);
            
        }

        if($request->hasFile('passport')){

            $driveName = $request->file('passport')->store('1ziwsyabHR3acrbkBrG4dPX2rsRYYMMGZ', 'google');
            $url = Storage::disk('google')->url($driveName);

            $passport = $request->file('passport');
            $passportname=time().'.'.$passport->getClientOriginalExtension();
            
            $student->passport = $url;
            $student->save();
            $attachment = Attachment::where('name','passport')->first();

            if($attachment)
            $studentAttachment = StudentAttachment::create(['student_id' => $student->id,'attachment_id' => $attachment->id, 'path' => $url,'start_date' => $request->passport_start_date, 'end_date' => $request->passport_end_date]);

        }

        if($request->hasFile('birth_certificate')){

            $driveName = $request->file('birth_certificate')->store('1_zuv-YXmQX4HOny9EPaoNWWrZ17xv85p', 'google');
            $url = Storage::disk('google')->url($driveName);

            $birth_certificate = $request->file('birth_certificate');
            $birth_certificatename=time().'.'.$birth_certificate->getClientOriginalExtension();

            $student->birth_certificate = $url;
            $student->save();
            $attachment = Attachment::where('name','birth_certificate')->first();

            if($attachment)
            $studentAttachment = StudentAttachment::create(['student_id' => $student->id,'attachment_id' => $attachment->id, 'path' => $url]);
        }

        if($request->hasFile('bank_statement')){

            $driveName = $request->file('bank_statement')->store('1v5OKUfNq5xai8g87tYK2wh_C29aRU9Wa', 'google');
            $url = Storage::disk('google')->url($driveName);

            $bank_statement = $request->file('bank_statement');
            $bank_statementname=time().'.'.$bank_statement->getClientOriginalExtension();

            $student->bank_statement = $url;
            $student->save();
            $attachment = Attachment::where('name','bank_statement')->first();

            if($attachment)
            $studentAttachment = StudentAttachment::create(['student_id' => $student->id,'attachment_id' => $attachment->id, 'path' => $url]);
        }
        
         if($request->hasFile('medical_insurance')){

            $driveName = $request->file('medical_insurance')->store('1v5OKUfNq5xai8g87tYK2wh_C29aRU9Wa', 'google');
            $url = Storage::disk('google')->url($driveName);

            $medical_insurance = $request->file('medical_insurance');
            $medical_insurance_name = time().'.'.$medical_insurance->getClientOriginalExtension();

            $student->medical_insurance = $url;
            $student->save();
            $attachment = Attachment::where('name','medical_insurance')->first();
            // dd($request->medical_insurance_start_date);
            if($attachment)
            $studentAttachment = StudentAttachment::create(['student_id' => $student->id,'attachment_id' => $attachment->id, 'path' => $url,'start_date' => $request->medical_insurance_start_date, 'end_date' => $request->medical_insurance_end_date]);
        }
         if($request->hasFile('spl')){

            $driveName = $request->file('spl')->store('1v5OKUfNq5xai8g87tYK2wh_C29aRU9Wa', 'google');
            $url = Storage::disk('google')->url($driveName);

            $spl = $request->file('spl');
            $spl_name=time().'.'.$spl->getClientOriginalExtension();

            $student->spl = $url;
            $student->save();
            $attachment = Attachment::where('name','spl')->first();

            if($attachment)
            $studentAttachment = StudentAttachment::create(['student_id' => $student->id,'attachment_id' => $attachment->id, 'path' => $url,'start_date' => $request->spl_start_date, 'end_date' => $request->spl_end_date]);
        }
           if($request->hasFile('ppl')){

            $driveName = $request->file('ppl')->store('1v5OKUfNq5xai8g87tYK2wh_C29aRU9Wa', 'google');
            $url = Storage::disk('google')->url($driveName);

            $ppl = $request->file('ppl');
            $ppl_name=time().'.'.$ppl->getClientOriginalExtension();

            $student->ppl = $url;
            $student->save();
            $attachment = Attachment::where('name','ppl')->first();

            if($attachment)
            $studentAttachment = StudentAttachment::create(['student_id' => $student->id,'attachment_id' => $attachment->id, 'path' => $url,'start_date' => $request->ppl_start_date, 'end_date' => $request->ppl_end_date]);
        }
          if($request->hasFile('cpl')){

            $driveName = $request->file('cpl')->store('1v5OKUfNq5xai8g87tYK2wh_C29aRU9Wa', 'google');
            $url = Storage::disk('google')->url($driveName);

            $cpl = $request->file('cpl');
            $cpl_name=time().'.'.$cpl->getClientOriginalExtension();

            $student->cpl = $url;
            $student->save();
            $attachment = Attachment::where('name','cpl')->first();

            if($attachment)
            $studentAttachment = StudentAttachment::create(['student_id' => $student->id,'attachment_id' => $attachment->id, 'path' => $url,'start_date' => $request->cpl_start_date, 'end_date' => $request->cpl_end_date]);
        }
          if($request->hasFile('contract')){

            $driveName = $request->file('contract')->store('1v5OKUfNq5xai8g87tYK2wh_C29aRU9Wa', 'google');
            $url = Storage::disk('google')->url($driveName);

            $contract = $request->file('contract');
            $contract_name =time().'.'.$contract->getClientOriginalExtension();

            $student->contract = $url;
            $student->save();
            $attachment = Attachment::where('name','contract')->first();

            if($attachment)
            $studentAttachment = StudentAttachment::create(['student_id' => $student->id,'attachment_id' => $attachment->id, 'path' => $url]);
        }

     
        

          $installments = $request->installments;
            foreach($installments as $key => $value){
                $installment = Installment::create(['student_id' => $student->id, 'value' => $value, 'currency_id' => $request->currency_id ]);
            }



        session()->flash('success', __('site.added_successfully'));
        return redirect()->route('dashboard.students.index');
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeApi(Request $request)
    {
        //add doctor to user table
        $request->validate([
            'name' => 'required',
            'email' => 'required|unique:users',
            'username' => 'required|unique:users',
            'phone' => 'required|unique:users',
            'active' => 'required',

            'password' => 'required|confirmed',
            //'permissions' => 'required|min:1',

        ]);
        $request_data = $request->except(['password', 'password_confirmation', 'permissions']);
        $request_data['password'] = bcrypt($request->password);
        $request_data['type'] = 'student';
        //$request_data['fid']  = $student->id;


        $user = User::create($request_data);
        $user->attachRole('student');
        //$user->syncPermissions($request->permissions);

        $request->validate([
            'name' => 'required',
            'level_id' => 'required',
            'department_id' => 'nullable',
            'code' => 'required||unique:students',
            'email' => 'required|unique:students',
            'username' => 'required|unique:students',
            'phone' => 'required|unique:students',
            'active' => 'required',
            'account_confirm' => 'required',
            'national_id' => 'required',
            'set_number' => 'required',

            'password' => 'required|confirmed',
            //'permissions' => 'required|min:1',

        ]);

        $request_data = $request->except(['password', 'password_confirmation', 'permissions']);
        $request_data['password'] = bcrypt($request->password);

        if ($request_data['active'] == 'on' || $request_data['active'] == 1)
            $request_data['active'] = 1;
        else
        $request_data['active'] = 0;



        $student = Student::create($request_data);
        $student->attachRole('student');
        $user->update(['fid'=>$student->id]);

        //$student->syncPermissions($request->permissions);


        return [
            "status" => 0,
            "message" => __('site.added_successfully')
        ];
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Student  $student
     * @return \Illuminate\Http\Response
     */
    public function edit(Student $student)
    {
        $levels = Level::all();
        $departments = Department::all();
        $governments = Government::all();
        $nationalities = Nationality::all();
        $currencies = Currency::all();
        $recommendes = Recommende::all();
        $military_status = DB::table('military_status')->get();
        return view('dashboard.students.edit', compact('student','levels', 'departments','governments','nationalities','currencies','recommendes','military_status'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Student  $student
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Student $student, User $user)
    {
        $request->validate([
            'name' => 'required',
            'level_id' => 'required',
            'department_id' => 'required',
            'code' => ['required', Rule::unique('students')->ignore($student->id)],
            // 'email' => ['required', Rule::unique('students')->ignore($student->id)],
            'username' => ['required', Rule::unique('students')->ignore($student->id)],
            'phone' => ['required', Rule::unique('students')->ignore($student->id)],
            'national_id' => 'required',
            // 'set_number' => 'required',
            'active' => 'required',
            'password' => 'required'

        ]);

        $request_data = $request->except(['permissions','password']);
        // $request_data['password'] = bcrypt($request->password);

        if($request->password == $student->password){
                $request_data['password'] = $student->password;
        }else{
                $request_data['password'] = bcrypt($request->password);
        }

        if ($request_data['active'])
            $request_data['active'] = 1;
        else
            $request_data['active'] = 0;
        
        if($request->hasFile('criminal_records')){

            $driveName = $request->file('criminal_records')->store('19YZc9HcWSQlaQG_BfMZVUXtY9zhEj6Qz', 'google');
            $url = Storage::disk('google')->url($driveName);

            $criminal_records = $request->file('criminal_records');
            $criminal_recordsname=time().'.'.$criminal_records->getClientOriginalExtension();

            $request_data['criminal_records'] = $url;
            
            $attachment = Attachment::where('name','criminal_records')->first();

            if($attachment)
            $studentAttachment = StudentAttachment::create(['student_id' => $student->id,'attachment_id' => $attachment->id, 'path' => $url]);
        }

        if($request->hasFile('passport')){

            $driveName = $request->file('passport')->store('1ziwsyabHR3acrbkBrG4dPX2rsRYYMMGZ', 'google');
            $url = Storage::disk('google')->url($driveName);

            $passport = $request->file('passport');
            $passportname=time().'.'.$passport->getClientOriginalExtension();

            $request_data['passport'] = $url;
            
             $attachment = Attachment::where('name','passport')->first();

            if($attachment)
            $studentAttachment = StudentAttachment::create(['student_id' => $student->id,'attachment_id' => $attachment->id, 'path' => $url,'start_date' => $request->passport_start_date, 'end_date' => $request->passport_end_date]);
        }

        if($request->hasFile('birth_certificate')){

            $driveName = $request->file('birth_certificate')->store('1_zuv-YXmQX4HOny9EPaoNWWrZ17xv85p', 'google');
            $url = Storage::disk('google')->url($driveName);

            $birth_certificate = $request->file('birth_certificate');
            $birth_certificatename=time().'.'.$birth_certificate->getClientOriginalExtension();

            $request_data['birth_certificate'] = $url;
            
             $attachment = Attachment::where('name','birth_certificate')->first();

            if($attachment)
            $studentAttachment = StudentAttachment::create(['student_id' => $student->id,'attachment_id' => $attachment->id, 'path' => $url]);
        }

        if($request->hasFile('bank_statement')){

            $driveName = $request->file('bank_statement')->store('1v5OKUfNq5xai8g87tYK2wh_C29aRU9Wa', 'google');
            $url = Storage::disk('google')->url($driveName);

            $bank_statement = $request->file('bank_statement');
            $bank_statementname=time().'.'.$bank_statement->getClientOriginalExtension();

            $request_data['bank_statement'] = $url;
            
            $attachment = Attachment::where('name','bank_statement')->first();

            if($attachment)
            $studentAttachment = StudentAttachment::create(['student_id' => $student->id,'attachment_id' => $attachment->id, 'path' => $url]);
        }
        if($request->hasFile('medical_insurance')){

            $driveName = $request->file('medical_insurance')->store('1v5OKUfNq5xai8g87tYK2wh_C29aRU9Wa', 'google');
            $url = Storage::disk('google')->url($driveName);

            $medical_insurance = $request->file('medical_insurance');
            $medical_insurance_name = time().'.'.$medical_insurance->getClientOriginalExtension();

            $request_data['medical_insurance'] = $url;
            
            $attachment = Attachment::where('name','medical_insurance')->first();

            if($attachment)
            $studentAttachment = StudentAttachment::create(['student_id' => $student->id,'attachment_id' => $attachment->id, 'path' => $url,'start_date' => $request->medical_insurance_start_date, 'end_date' => $request->medical_insurance_end_date]);
        }
         if($request->hasFile('spl')){

            $driveName = $request->file('spl')->store('1v5OKUfNq5xai8g87tYK2wh_C29aRU9Wa', 'google');
            $url = Storage::disk('google')->url($driveName);

            $spl = $request->file('spl');
            $spl_name=time().'.'.$spl->getClientOriginalExtension();

            $request_data['spl'] = $url;
            
            $attachment = Attachment::where('name','spl')->first();

            if($attachment)
            $studentAttachment = StudentAttachment::create(['student_id' => $student->id,'attachment_id' => $attachment->id, 'path' => $url,'start_date' => $request->spl_start_date, 'end_date' => $request->spl_end_date]);
        }
           if($request->hasFile('ppl')){

            $driveName = $request->file('ppl')->store('1v5OKUfNq5xai8g87tYK2wh_C29aRU9Wa', 'google');
            $url = Storage::disk('google')->url($driveName);

            $ppl = $request->file('ppl');
            $ppl_name=time().'.'.$ppl->getClientOriginalExtension();

            $request_data['ppl'] = $url;
            $attachment = Attachment::where('name','ppl')->first();

            if($attachment)
            $studentAttachment = StudentAttachment::create(['student_id' => $student->id,'attachment_id' => $attachment->id, 'path' => $url,'start_date' => $request->ppl_start_date, 'end_date' => $request->ppl_end_date]);
        }
        if($request->hasFile('cpl')){

            $driveName = $request->file('cpl')->store('1v5OKUfNq5xai8g87tYK2wh_C29aRU9Wa', 'google');
            $url = Storage::disk('google')->url($driveName);

            $cpl = $request->file('cpl');
            $cpl_name=time().'.'.$cpl->getClientOriginalExtension();

            $request_data['cpl'] = $url;
            
             $attachment = Attachment::where('name','cpl')->first();

            if($attachment)
            $studentAttachment = StudentAttachment::create(['student_id' => $student->id,'attachment_id' => $attachment->id, 'path' => $url,'start_date' => $request->cpl_start_date, 'end_date' => $request->cpl_end_date]);
        }
          if($request->hasFile('contract')){

            $driveName = $request->file('contract')->store('1v5OKUfNq5xai8g87tYK2wh_C29aRU9Wa', 'google');
            $url = Storage::disk('google')->url($driveName);

            $contract = $request->file('contract');
            $contract_name =time().'.'.$contract->getClientOriginalExtension();

            $request_data['contract'] = $url;
            
              $attachment = Attachment::where('name','contract')->first();

            if($attachment)
            $studentAttachment = StudentAttachment::create(['student_id' => $student->id,'attachment_id' => $attachment->id, 'path' => $url]);
        }

        $student->update($request_data);

        //update student in user table
        $request->validate([
            'name' => 'required',
            //'last_name' => 'required',
            // 'email' => 'required',
            //'username' => 'required|unique:users',
            'phone' => 'required',
            'active' => 'required',
            'password'=> 'required'
        ]);



        $user = User::where('fid',$student->id)
                    ->where('type', 'student')->first();

        $request_data = $request->except(['permissions','password']);
        // $request_data['password'] = bcrypt($request->password);
          if($request->password != $student->password){
                    $request_data['password'] = bcrypt($request->password);
                       // dd('not same');
                }else{
                    $request_data['password'] = $student->password;
                        // dd('same');
                }
        if ($request_data['active'] == 'on')
            $request_data['active'] = 1;
        else
            $request_data['active'] = 0;

        $user->update($request_data);


        session()->flash('success', __('site.updated_successfully'));
        return redirect()->route('dashboard.students.index');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Student  $student
     * @return \Illuminate\Http\Response
     */
    public function updateApi(Request $request, Student $student)
    {
        $request->validate([
            'name' => 'required',
            'level_id' => 'required',
            'department_id' => 'required',
            'code' => ['required', Rule::unique('students')->ignore($student->id)],
            'email' => ['required', Rule::unique('students')->ignore($student->id)],
            'username' => ['required', Rule::unique('students')->ignore($student->id)],
            'phone' => ['required', Rule::unique('students')->ignore($student->id)],
            'national_id' => 'required',
            'set_number' => 'required',
            'active' => 'required',

        ]);
        if ($request_data['active'] == 'on' || $request_data['active'] == 1)
            $request_data['active'] = 1;
        else
        $request_data['active'] = 0;

        $request_data = $request->except(['permissions']);
        //$request_data['phone'] = array_filter($request->phone);

        $student->update($request_data);
        $user = $student->user;

        //update student in user table
        $request->validate([
            'name' => 'required',
            //'last_name' => 'required',
            'email' => 'required',
            //'username' => 'required|unique:users',
            'phone' => 'required',
            'active' => 'required',

        ]);

        if ($request_data['active'] == 'on' || $request_data['active'] == 1)
            $request_data['active'] = 1;
        else
        $request_data['active'] = 0;

        $request_data= $request->except(['permissions']);
        optional($student->user)->update($request_data);


        return [
            "status" => 0,
            "message" => __('site.updated_successfully')
        ];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Student  $student
     * @return \Illuminate\Http\Response
     */
    /*public function destroy(Student $student)
    {
        if ($student->stdAssign()->exists())
            {
                notify()->error(trans('site.can_not_delete_related_items'),"Error","topRight");
                return redirect()->route('dashboard.students.index');

            }else{
                $users = User::all();
                foreach ($users as $user) {
                    if($user->fid == $student->id){
                        $user->delete();
                    }
                }
                $student->delete();
                session()->flash('success', __('site.deleted_successfully'));
                return redirect()->route('dashboard.students.index');
            }
    }*/

    //change active function
    public function destroy(Student $student)
    {
        if ($student->stdAssign()->exists())
            {
                notify()->error(trans('site.can_not_delete_related_items'),"Error","topRight");
                return redirect()->route('dashboard.students.index');

            }else{
                $user = User::where('fid', $student->id)
                            ->where('type', 'student')->first();

                $user->delete();


                $student->delete();
                session()->flash('success', __('site.deleted_successfully'));
                return redirect()->route('dashboard.students.index');
            }
    }
}
