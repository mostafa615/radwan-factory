<?php

namespace App\Http\Controllers\Dashboard;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function update(Request $request) {
        try {
            $user = Auth::user();
            $user->update($request->all());

            //notify(__('update profile info'), __('you update profile info'), "fa fa-id-card-o");
            session()->flash('success', __('site.updated_successfully'));
            // return Message::success(Message::$DONE);
        } catch (\Exception $ex) {
            //return Message::error(Message::$ERROR . $ex->getMessage());
        }
    }

    public function updatePhone(Request $request) {
        try {
            $validator = validator()->make($request->all(), [
                'phone' => 'required|unique:users',
                'confirm_phone' => 'required',
            ], [
                "phone.required" => __("phone_required"),
                "new_phone.unique" => __("phone_already_exist"),
                "confirm_phone.required" => __("phone_required"),
            ]);

            if ($validator->fails()) {
                $key = $validator->errors()->first();
                //return Message::error(__($key));
            }

            if ($request->phone != $request->confirm_phone)
                //return Message::error(__('phones not match'));

            $user = Auth::user();

            $user->update([
                "phone" => $request->phone
            ]);
            //notify(__('update profile info'), __('you update profile info'), "fa fa-id-card-o");
            session()->flash('success', __('site.updated_successfully'));
            //return Message::success(Message::$DONE);
        } catch (\Exception $ex) {
            //return Message::error(Message::$ERROR . $ex->getMessage());
        }
    }

    public function updatePassword(Request $request) {
        try {
            $validator = validator()->make($request->all(), [
                'old_password' => 'required',
                'new_password' => 'required|min:8',
                'confirm_password' => 'required',
            ], [
                "old_password.required" => __("old_password_required"),
                "new_password.required" => __("new_password_required"),
                "new_password.min" => __("min password character is 8"),
            ]);

            if ($validator->fails()) {
                $key = $validator->errors()->first();
                //return Message::error(__($key));
            }

            if ($request->new_password != $request->confirm_password)
                //return Message::error(__('passwords not match'));

            $user = Auth::user();

            if ($request->old_password != $user->password)
                //return Message::error(__('your old password is not correct'));


            $user->update([
                "password" => $request->new_password
            ]);

            //notify(__('update profile info'), __('you update profile info'), "fa fa-id-card-o");
            session()->flash('success', __('site.updated_successfully'));

            //return Message::success(Message::$DONE);
        } catch (\Exception $ex) {
            //return Message::error(Message::$ERROR . $ex->getMessage());
        }
    }


}
