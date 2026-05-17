<?php

namespace App\Http\Controllers\Dashboard;

use App\Admin;
use App\Doctor;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Student;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use SebastianBergmann\Environment\Console;
use Validator;
use App\Mail\ActiveMail;
use Illuminate\Support\Facades\Mail;
//use Mail;
class UserController extends Controller
{
    public function __construct(){
        $this->middleware(['permission:read_users'])->only('index');
        $this->middleware(['permission:create_users'])->only('create');
        $this->middleware(['permission:update_users'])->only('edit');
        $this->middleware(['permission:delete_users'])->only('destroy');

    }

    public function index(Request $request)
    {

        $users = User::whereRoleIs('admin')->where(function($q) use ($request){

            return $q->when($request->search, function($query) use ($request){

                return $query->where('name', 'like', '%'. $request->search .'%')
                    ->orWhere('last_name', 'like', '%'. $request->search .'%');

            });
        })->latest()->paginate(4);

        return view('dashboard.users.index', compact('users'));

    }//end of index

    //change name function
    public function changeName(Request $request,$id){
        $user = User::find($id);
        $rules = array(
            'name' => 'required'
        );
        $request->validate([
            'name' => 'required',
        ]);

        $error = Validator::make($request->all(), $rules);

        if($error->fails()){
            return response()->json(['errors' => $error->errors()->all()]);
        }

        $form_data = array(
            'name' => $request->name,
        );

        $user->update($form_data);

        //if user is doctor
        if($user->type == 'doctor'){
            $doc = Doctor::find($user->fid);
            $rules = array(
                'name' => 'required'
            );
            $request->validate([
                'name' => 'required',
            ]);
            $error = Validator::make($request->all(), $rules);
            if($error->fails()){
                return response()->json(['errors' => $error->errors()->all()]);
            }

            $form_data = array(
                'name' => $request->name,
            );
            $doc->update($form_data);
            //return redirect()->back();
            return response()->json(['success'=>'Data Updated Succefully']);

        }//end of if

        //if user is student
        if($user->type == 'student'){
            $std = Student::find($user->fid);
            $rules = array(
                'name' => 'required'
            );
            $request->validate([
                'name' => 'required',
            ]);
            $error = Validator::make($request->all(), $rules);
            if($error->fails()){
                return response()->json(['errors' => $error->errors()->all()]);
            }

            $form_data = array(
                'name' => $request->name,
            );
            $std->update($form_data);
            //return redirect()->back();
            return response()->json(['success'=>'Data Updated Succefully']);

        }//end of if

        //if user is admin
        if($user->type == 'admin'){
            $admin = Admin::find($user->fid);
            $rules = array(
                'name' => 'required'
            );
            $request->validate([
                'name' => 'required',
            ]);
            $error = Validator::make($request->all(), $rules);
            if($error->fails()){
                return response()->json(['errors' => $error->errors()->all()]);
            }

            $form_data = array(
                'name' => $request->name,
            );
            $admin->update($form_data);
            //return redirect()->back();
            return response()->json(['success'=>'Data Updated Succefully']);

        }//end of if

        //session()->flash('success', __('site.updated_successfully'));
        //return redirect()->route('dashboard.index');

    }//end of change profile name function

    public function changePass(Request $request, $id){
        $user = User::find($id);
        $rules = array(
            'old_password' => 'required',
            //'password' => 'required|confirmed',
        );
        $request->validate([
            'old_password' => 'required',
            'password' => 'required|confirmed',
        ]);

        $request_data = $request->except(['password', 'password_confirmation']);
        $request_data['password'] = bcrypt($request->password);

        $error = Validator::make($request->except(['password', 'password_confirmation']), $rules);

        if(Hash::check($request->old_password, $user->password) ){
            if($request->password == $request->password_confirmation){
                $user->update($request_data);
                //return response()->json(['success'=>'Data Updated Succefully']);
            }else{
                return response()->json(['errors' => 'حقل التاكيد غير متطابق' ]);
            }
        }else{
            return response()->json(['errors' => 'the old password is false' ]);
         }
        if($error->fails()){
            return response()->json(['errors' => $error->errors()->all()]);
        }

        //if user is doctor
        if($user->type == 'doctor'){
            $doc = Doctor::find($user->fid);
            $rules = array(
                'old_password' => 'required',
                //'password' => 'required|confirmed',
            );
            $request->validate([
                'old_password' => 'required',
                'password' => 'required|confirmed',
            ]);

            $request_data = $request->except(['password', 'password_confirmation']);
            $request_data['password'] = bcrypt($request->password);

            $error = Validator::make($request->except(['password', 'password_confirmation']), $rules);

            if(Hash::check($request->old_password, $doc->password) ){
                if($request->password == $request->password_confirmation){
                    $doc->update($request_data);
                    return response()->json(['success'=>'Data Updated Succefully']);
                }else{
                    return response()->json(['errors' => 'حقل التاكيد غير متطابق' ]);
                }
            }else{
                return response()->json(['errors' => 'the old password is false' ]);
            }
            if($error->fails()){
                return response()->json(['errors' => $error->errors()->all()]);
            }
        }//end of doctor type

        //if user is student
        if($user->type == 'student'){
            $std = Student::find($user->fid);
            $rules = array(
                'old_password' => 'required',
                //'password' => 'required|confirmed',
            );
            $request->validate([
                'old_password' => 'required',
                'password' => 'required|confirmed',
            ]);

            $request_data = $request->except(['password', 'password_confirmation']);
            $request_data['password'] = bcrypt($request->password);

            $error = Validator::make($request->except(['password', 'password_confirmation']), $rules);

            if(Hash::check($request->old_password, $std->password) ){
                if($request->password == $request->password_confirmation){
                    $std->update($request_data);
                    return response()->json(['success'=>'Data Updated Succefully']);
                }else{
                    return response()->json(['errors' => 'حقل التاكيد غير متطابق' ]);
                }
            }else{
                return response()->json(['errors' => 'the old password is false' ]);
            }
            if($error->fails()){
                return response()->json(['errors' => $error->errors()->all()]);
            }
        }//end of student type

        //if user is admin
        if($user->type == 'admin'){
            $admin = Admin::find($user->fid);
            $rules = array(
                'old_password' => 'required',
                //'password' => 'required|confirmed',
            );
            $request->validate([
                'old_password' => 'required',
                'password' => 'required|confirmed',
            ]);

            $request_data = $request->except(['password', 'password_confirmation']);
            $request_data['password'] = bcrypt($request->password);

            //$error = Validator::make($request->except(['password', 'password_confirmation']), $rules);

            if(Hash::check($request->old_password, $admin->password) ){
                if($request->password == $request->password_confirmation){
                    $admin->update($request_data);
                    return response()->json(['success'=>'Data Updated Succefully']);
                }else{
                    return response()->json(['errors' => 'حقل التاكيد غير متطابق' ]);
                }
            }else{
                return response()->json(['errors' => 'the old password is false' ]);
            }
            if($error->fails()){
                return response()->json(['errors' => $error->errors()->all()]);
            }
        }//end of admin type

    }//end of change password function

    public function changeEmail(Request $request, $id){
        $user = User::find($id);
        $rules = array(
            'email' => ['required', Rule::unique('users')->ignore($user->id)],
        );
        $request->validate([
            'email' => ['required', Rule::unique('users')->ignore($user->id)],
        ]);

        $request_data = $request->all();
        //$active_code = rand(1,6);
        //$request_data['active_code'] = str_random(4);

        $error = Validator::make($request->all(), $rules);


        if($error->fails()){
            return response()->json(['errors' => $error->errors()->all()]);
        }

        $user->active_code = str_random(4);
        $user->update([
            "email" => $request->email
        ]);

        //return response()->json(['success'=>'Data Updated Succefully']);

        //if user is doctor
        $docID = $user->fid;
        if(auth()->user()->type == 'doctor'){
            $doc = Doctor::find($docID);
            $rules = array(
                'email' => 'required',
            );
            $request->validate([
                'email' => 'required',
            ]);

            $request_data = $request->all();

            $error = Validator::make($request->all(), $rules);

            if($error->fails()){
                return response()->json(['errors' => $error->errors()->all()]);
            }
            $doc->active_code = $user->active_code;

            //echo "HTML Email Sent. Check your inbox.";
            $doc->update([
                "email" => $request->email
            ]);

            $data = array('name'=>"Seyouf");
            $message = 'كود التفعيل هو : ' . $user->active_code;
            Mail::raw($message, function ($message) use ($user) {
                $message->to($user->email)
                        ->subject('مرحبا بك في أكاديمية السيوف');
            });

            /*$data = array('name'=>"Seyouf");
            $message = 'كود التفعيل : ' . $user->active_code;
            Mail::send(['text'=>'mail'], $data, function($message) use ($user) {
                $message->to($user->email)
                        ->subject('كود التفعيل : ' . $user->active_code);
            });*/

            /*$message = 'كود التفعيل :' . $user->active_code;
            Mail::raw($message, function ($message) use ($user) {
                $message->to($user->email)->send('كود التفعيل : ' . $user->active_code);
            });*/

            return response()->json(['success'=>'Data Updated Succefully']);

        }//end of doctor type

        //if user is student
        if($user->type == 'student'){
            $std = Student::find($user->fid);
            $rules = array(
                'email' => 'required',
            );
            $request->validate([
                'email' => 'required',
            ]);

            $request_data = $request->all();
            $error = Validator::make($request->all(), $rules);

            if($error->fails()){
                return response()->json(['errors' => $error->errors()->all()]);
            }

            //$std->update($request_data);
            //$std->active_code = $user->active_code;
            //$std->update($request_data);
            $std->active_code = $user->active_code;
            $std->update([
                "email" => $request->email
            ]);

            $data = array('name'=>"Seyouf");
            $message = 'كود التفعيل هو : ' . $user->active_code;
            Mail::raw($message, function ($message) use ($user) {
                $message->to($user->email)
                        ->subject('مرحبا بك في أكاديمية السيوف');
            });

            /*$data = array('name'=>"Seyouf");
            Mail::send(['text'=>'mail'], $data, function($message) use ($user) {
                $message->to($user->email)
                        ->subject('كود التفعيل : ' . $user->active_code);
            });*/

            return response()->json(['success'=>'Data Updated Succefully']);


        }//end of student type

        //if user is admin
        if($user->type == 'admin'){
            $admin = Admin::find($user->fid);
            $rules = array(
                'email' => 'required',
            );
            $request->validate([
                'email' => 'required',
            ]);

            $request_data = $request->all();
            //$request_data['active_code'] = str_random(4);
            $error = Validator::make($request->all(), $rules);

            if($error->fails()){
                return response()->json(['errors' => $error->errors()->all()]);
            }
            //$admin->active_code = $user->active_code;
            //$admin->update($request_data);
            $admin->active_code = $user->active_code;
            $admin->update([
                "email" => $request->email
            ]);

            $data = array('name'=>"Seyouf");
            $message = 'كود التفعيل هو : ' . $user->active_code;
            Mail::raw($message, function ($message) use ($user) {
                $message->to($user->email)
                        ->subject('مرحبا بك في أكاديمية السيوف');
            });

            /*$data = array('name'=>"Seyouf");
            Mail::send(['text'=>'mail'], $data, function($message) use ($user) {
                $message->to($user->email)
                        ->subject('كود التفعيل : ' . $user->active_code);

            Mail::send(['text'=>'mail'], $data, function($message) use ($admin) {
                $message->to($admin->email)
                        ->subject('كود التفعيل : ' . $admin->active_code);
            });*/

            //$admin->update(['email'=>$request->email, 'active_code'=>$active_code]);

            return response()->json(['success'=>'Data Updated Succefully']);


        }//end of admin type

    }//end of change email function

    public function chactcode(Request $request ,$id){
        $user = User::find($id);

        if($user->active_code == $request->sentcode){
            return response()->json(['success'=>'الكود متطابق']);
        }else{
            return response()->json(['errors' => $error->errors()->all()]);
        }
    }

    //function to change profile phone
    public function changePhone(Request $request, $id){
        $user = User::find($id);
        $rules = array(
            'old_phone' => 'required',
            'new_phone' => 'required'
        );
        $request->validate([
            'old_phone' => 'required',
            'new_phone' => 'required'
        ]);

        $error = Validator::make($request->all(), $rules);

        if($error->fails()){
            return response()->json(['errors' => $error->errors()->all()]);
        }

        if(! $user->phone == $request->old_phone){

         return response()->json(['errors' => 'the old phone is false' ]);
         }

         $form_data = array(
            'phone' => $request->new_phone,
        );
        $user->update($form_data);

         //if user is doctor
        if($user->type == 'doctor'){
            $doctor = Doctor::find($user->fid);

            $form_data = array(
                'phone' => $request->new_phone,
            );
            $doctor->update($form_data);

        }//end of doctor type

        if($user->type == 'student'){
            $std = Student::find($user->fid);

            $form_data = array(
                'phone' => $request->new_phone,
            );
            $std->update($form_data);

        }//end of doctor type

        if($user->type == 'admin'){
            $admin = Admin::find($user->fid);

            $form_data = array(
                'phone' => $request->new_phone,
            );
            $admin->update($form_data);

        }//end of doctor type
        return response()->json(['success'=>'Data Updated Succefully']);

    }//end of change profile phone function

    public function create()
    {
        return view('dashboard.users.create');
    }


    public function store(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required',
            //'last_name' => 'required',
            'email' => ['required', Rule::unique('users')->ignore($user->id)],
            'password' => 'required|confirmed',
        ]);

        $request_data= $request->except(['password', 'password_confirmation', 'permissions']);
        $request_data['password'] = bcrypt($request->password);

        $user = User::create($request_data);
        $user->attachRole('user');
        $user->syncPermissions($request->permissions);

        session()->flash('success', __('site.added_successfully'));

        return redirect()->route('dashboard.users.index');

    }/* end of store */


    public function edit(User $user)
    {
        return view('dashboard.users.edit', compact('user'));
    }


    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required',
            //'last_name' => 'required',
            'email' => 'required',
        ]);

        $request_data= $request->except(['permissions']);
        $user->update($request_data);

        $user->syncPermissions($request->permissions);

        session()->flash('success', __('site.updated_successfully'));

        return redirect()->route('dashboard.users.index');
    }


    public function destroy(User $user)
    {
        $user->delete();
        session()->flash('success', __('site.deleted_successfully'));
        return redirect()->route('dashboard.users.index');
    }
}
