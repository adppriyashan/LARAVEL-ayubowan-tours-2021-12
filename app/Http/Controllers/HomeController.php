<?php

namespace App\Http\Controllers;

use App\Models\Driver;
use App\Models\Invoice;
use App\Models\VehicleType;
use Carbon\Carbon;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $vehicles = VehicleType::where('status', 1)->count();
        $drivers = Driver::where('status', 1)->count();
        $monthSales = Invoice::where('status', 1)->whereMonth('date', Carbon::now()->month)->sum('grand_total');
        
        $date = Carbon::now()->timezone('Asia/Colombo')->subDays(10)->format('Y-m-d');
        
        $lastTenSales = Invoice::where('status', 1)->where('created_at', '>=', $date)->orderBy('id', 'DESC')->get();
        
        $dates = [];
        $sales = [];

        foreach ($lastTenSales as $key => $value) {
            $check = false;
            $index = 0;
            
            if(array_key_exists($value->created_at->timezone('Asia/Colombo')->format('Y-m-d'),$sales)){
                $sales[$value->created_at->timezone('Asia/Colombo')->format('Y-m-d')]=$sales[$value->created_at->timezone('Asia/Colombo')->format('Y-m-d')]+ $value->grand_total;
            }else{
                $sales[$value->created_at->timezone('Asia/Colombo')->format('Y-m-d')]=$value->grand_total;
            }
        }
        
        $dates=array_keys($sales);
        $sales=array_values($sales);

        $dates = array_reverse($dates);
        $sales = array_reverse($sales);

        return view('home', compact(['vehicles', 'drivers', 'monthSales', 'dates', 'sales']));
    }
}
