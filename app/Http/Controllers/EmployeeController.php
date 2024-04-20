<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Trip;
//use Illuminate\Contracts\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;


class EmployeeController extends Controller
{
    public function AddTrip(Request $request)
    {

        $validator =Validator::make($request->all(),[
            'branch_id'=>'required',
            'truck_id'=>'required',
            'driver_id'=>'required',
            'manifest_id'=> 'required',
            'trip_number'=>'required|string',
            'source'=>'required|string',
            'destination'=>'required|string',
           
        ]);
        if ($validator->fails())
        {
            return response()->json($validator->errors()->toJson(),400);
        }

        $employee = Auth::guard('employee')->user()->name;
            $trip=Trip::create([
                'branch_id'=>$request->branch_id,
                'truck_id'=>$request->truck_id,
                'driver_id'=>$request->driver_id,
                'manifest_id'=>$request->manifest_id,
                'number'=>$request->trip_number,
                'source'=> $request->source,
                'destination'=>$request->destination,
                'date'=>now()->format('Y-m-d'),
                'created_by'=>$employee,

           ]);
           return response()->json(['message'=>'trip addedd successfully', ],200);
    
    }
      
    public function EditTrip(Request $request)
    {

        $user = Auth::guard('employee')->user();
   
     
        $validator =Validator::make($request->all(),[
            'trip_id'=>'required',
            'branch_id'=>'numeric',
            'truck_id'=>'numeric',
            'driver_id'=>'numeric',
            'manifest_id'=>'numeric',
            'trip_number'=>'string',
            'source'=>'string',
            'destination'=>'string',
           'arrival_date'=>'date ',
           'status' => ['required',Rule::in(['active', 'closed', 'temporary'])  ],
      ]);
      if ($validator->fails())
      {
          return response()->json($validator->errors()->toJson(),400);
      }

    //   $arrival_date=$request->arrival_date;
  
        $trip = Trip::find($request->trip_id);
        $updatedtrip= $trip->update(array_merge($request->all() ,[
        'edited_by' => $user->name,
        'editing_date' => now()->format('Y-m-d')
        ]
      ));
  

  
        return response()->json(['message' => 'trip edited successfully']);
  
  
    }




    public function CancelTrip(Request $request)
    {
       
        
          $validator =Validator::make($request->all(),[
            'trip_id'=>'required',
        ]);
    
        if ($validator->fails())
        {
            return response()->json($validator->errors()->toJson(),400);
        }
    
            $trip = Trip::find($request->trip_id)->delete();
    
            return response()->json(['msg'=>'trip has been canceled'], 200) ;
        
    }


    public function GetBranches()
        {
            $branches = Branch::pluck('address');
            if ($branches->isEmpty()) {
                return response()->json(['message' => 'No branches found']);
            }
            return response()->json(['branches' => $branches]);

        }
        

        public function GetActiveTrips()
        {
           
            $trips = Trip::where('status', 'active')->get();

            
            if ($trips->isEmpty()) {
                return response()->json(['message' => 'No active trips found']);
            }

            return response()->json(['trips' => $trips]);
        }



     public function ArchiveData(Request $request)
           {
            
          $validator =Validator::make($request->all(),[
            'trip_id'=>'required',
               ]);
    
        if ($validator->fails())
        {
            return response()->json($validator->errors()->toJson(),400);
        }
    
             $record = Trip::findOrFail($request->trip_id);
             $record->archived = true;
             $record->save();


             return response()->json(['msg'=>'trip has been archived'], 200) ;
        
          }

   
          public function GetArchiveData(Request $request)
           {
            
            $archivedRecords = Trip::where('archived', true)->get();
            if ($archivedRecords->isEmpty()) {
               
                return response()->json(['message' => 'No archive trips found']);
            }

            return response()->json(['Archived trips' => $archivedRecords]);
       
    
             

             return response()->json([''], 200) ;
        
          }


}

