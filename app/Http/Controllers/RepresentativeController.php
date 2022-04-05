<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Representative;
use App\Models\RepresentativeAssigned;
use Freshbitsweb\Laratables\Laratables;
use Illuminate\Http\Request;

class RepresentativeController extends Controller
{
    public function index()
    {
        return view('pages.representative');
    }

    public function indexAssignRep()
    {
        $representatives = Representative::where('status',1)->get();
        return view('pages.assignrep',compact(['representatives']));
    }

    public function list(Request $request)
    {
        if($request->has('typeactions') && $request->typeactions=='2'){
            return Laratables::recordsOf(Representative::class,RepresentativeAssigned::class); 
        }else{
           return Laratables::recordsOf(Representative::class); 
        }
        
    }

    public function getOne(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:representatives,id'
        ]);
        return Representative::where('id', $request->id)->first();
    }

    public function deleteOne(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:representatives,id'
        ]);
        Representative::where('id', $request->id)->update(['status' => 4]);

        return redirect()->back()->with(['code' => 1, 'color' => 'danger', 'msg' => 'Representative Successfully Removed']);
    }
    
    public function enrollAssigned(Request $request)
    {
        $request->validate([
            'representative' => 'required|exists:representatives,id',
            'pin'=>'required|min:4'
        ]);
        
        $query=Representative::where('id', $request->representative);
        $rec=$query->first();
        if($rec){
            
            if($rec->pin!=$request->pin){
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'pin' => ['Invalid Pin Number']
                ]);
            }
            
            $query->update(['isassigned' => 1]);
        }
        
        return redirect()->back()->with(['code' => 1, 'color' => 'success', 'msg' => 'Representative Successfully Assigned']);
    }
    
    public function deleteAssignedOne(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:representatives,id'
        ]);
        Representative::where('id', $request->id)->update(['isassigned' => 2]);

        return redirect()->back()->with(['code' => 1, 'color' => 'danger', 'msg' => 'Assigned Representative Successfully Removed']);
    }

    public function enroll(Request $request)
    {
        $request->validate([
            'isnew' => 'required|in:1,2',
            'first_name' => 'required',
            'last_name' => 'required',
            'address_1' => 'required',
            'address_2' => 'nullable',
            'city' => 'required',
            'pin' => 'required|min:4',
            'mobile_number' => 'required',
            'email' => 'required|email',
            'status' => 'required|in:1,2'
        ]);

        $data = [
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'address1' => $request->address_1,
            'city' => $request->city,
            'pin'=>$request->pin,
            'mobile_number' => $request->mobile_number,
            'email' => $request->email,
            'status' => $request->status
        ];

        if ($request->has('address_2')) {
            $data['address2'] = $request->address_2;
        }

        if ($request->isnew == 1) {

            Representative::create($data);

            return redirect()->back()->with(['code' => 1, 'color' => 'success', 'msg' => 'Representative Successfully Registered']);
            
        } else {

            $request->validate([
                'record' => 'required|exists:representatives,id'
            ]);

            Representative::where('id', $request->record)->update($data);

            return redirect()->back()->with(['code' => 1, 'color' => 'warning', 'msg' => 'Representative Successfully Updated']);
            
        }
    }
}
