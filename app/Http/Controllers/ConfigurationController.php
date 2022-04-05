<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Configuration;

class ConfigurationController extends Controller
{
    public function index(){
        $data=Configuration::first();
        return view('pages.configuration')->with('data',$data);
    }
    
    public function enroll(Request $request){
        $request->validate([
            'lkr_value'=>'required|numeric|min:0',
            'show_currency'=>'required|in:1,2',
            'list_count'=>'required|in:10,25,50'
        ]);
        
        Configuration::first()->update(['lkrvalueofusd'=>$request->lkr_value,'showcurrency'=>$request->show_currency,'listrecordscount'=>$request->list_count]);
        
        return redirect()->back()->with(['code' => 1, 'color' => 'success', 'msg' => 'Configuration Updated.']);
    }
}
