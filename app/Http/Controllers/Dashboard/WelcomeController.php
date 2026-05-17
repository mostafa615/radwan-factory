<?php

namespace App\Http\Controllers\Dashboard;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class WelcomeController extends Controller
{
    //
    public function index(){
        $loginHistories = auth()->user()->loginHistories()->latest('id')->get();
        return view('dashboard.index', compact('loginHistories'));
    }
}
