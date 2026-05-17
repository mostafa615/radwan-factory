<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return redirect()->route('dashboard.index');
});

Auth::routes(['register' => false]);

Route::get('/home', 'HomeController@index')->name('home');



Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

///Route::group(['prefix' => LaravelLocalization::setLocale()], );
Route::get('/studentLogin', 'StudentLoginController@index')->name('studentLogin');


// student post login
Route::get('/remote', 'Dashboard\StudentRemoteLoginController@login')->name('studentRemoteLogin');

// problem routes
Route::resource('dashboard/complains', 'dashboard\ComplainController')->except(['show']);
Route::post("dashboard/complain/store", "dashboard\ComplainController@store");

Route::get("dashboard/complains/student-problem", "dashboard\ComplainController@student");
Route::get("dashboard/complains/doctor-problem", "dashboard\ComplainController@doctor");
Route::get("dashboard/student-problem/data", "dashboard\ComplainController@getDataStudent");
Route::get("dashboard/doctor-problem/data", "dashboard\ComplainController@getDataDoctor");
Route::post("dashboard/problem/update/{problem}", "dashboard\ComplainController@update");
