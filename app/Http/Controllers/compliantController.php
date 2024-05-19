<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class compliantController extends Controller
{
    public function AddCompliant(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'message'=>'required',
            
        ]);

        if ($validator->fails())
        {
            return response()->json($validator->errors()->toJson(),400);
        }
        $customer_id=Auth::guard('customer')->user()->id;
        $comp=Complaint::Create([
                'customer_id'=>$customer_id,
                'message'=>$request->message,

        ]);

        return response()->json([
            'message'=>'addedd successfully',
        ],201);



    }


    public function AddCompliantEmp(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'message'=>'required',
            
        ]);

        if ($validator->fails())
        {
            return response()->json($validator->errors()->toJson(),400);
        }
        $emp_id=Auth::guard('employee')->user()->id;
        $comp=Complaint::Create([
                'customer_id'=>$emp_id,
                'message'=>$request->message,

        ]);

        return response()->json([
            'message'=>'addedd successfully',
        ],201);

  }


}