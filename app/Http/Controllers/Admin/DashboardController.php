<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use DB;

class DashboardController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    { 
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
	 
    public function index(){

        // $course = \App\Models\Course::where('status','1')->count();
        // $batch = \App\Models\Batch::where('status','1')->count();
        // $user = \App\Models\User::where('status','1')->count();
        $permission = session('permission') ?? [];
        return view('admin.dashboard',compact('permission'));
    } 
    
}