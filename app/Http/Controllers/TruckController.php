<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Truck;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TruckController extends Controller
{
    public function AddTruck(Request $request){

        $validator =Validator::make($request->all(),[
            'branch_id'=>'required',
            'number'=>'required|min:4|max:20',
            'line'=>'string|required',
            'notes'=>'string',
        ]);
            $createdby = Auth::guard('branch_manager')->user()->name;
            $truck=Truck::create([
                'branch_id'=>$request->branch_id,
                'number'=>$request->number,
                'line'=> $request->line,
                'notes'=>$request->notes,
                'created_by'=>$createdby,
                'adding_data'=>now()->format('Y-m-d'),
                
            ]);
          
            return response()->json([
                'message'=>'Truck addedd successfully',
            ],201);
        }


    public function UpdateTruck(Request $request){

            $validator =Validator::make($request->all(),[
                'truck_id'=>'required',
                'number'=>'min:4|max:20',
                'line'=>'string',
                'notes'=>'string',
            ]);
    
            if ($validator->fails())
            {
                return response()->json($validator->errors()->toJson(),400);
            }
          
          
            $edit_by = Auth::guard('branch_manager')->user()->name;
             
            $truck = Truck::where('id' , $request->truck_id)->first();
    
            if($truck){
    
                $updated_truck = $truck->update( array_merge(
                    $validator->validated(),
                    ['editing_by'=>$edit_by,
                    'editing_date'=>now()->format('Y-m-d'),
                    ]
                ));
    
            }
          
            return response()->json([
                'message'=>'Truck updated successfully',
            ],201);
        
           
    }

    public function DeleteTruck(Request $request){

        $validator =Validator::make($request->all(),[
            'truck_id'=>'required',
        ]);

        if ($validator->fails())
        {
            return response()->json($validator->errors()->toJson(),400);
        }


        $truck = Truck::find($request->truck_id)->delete();
      
        return response()->json([
            'message'=>'Truck deleted successfully',
        ],201);
    }

    

      public function  GetTruckInformation(Request $request)
    {
        $validator =Validator::make($request->all(),[
            'truck_number'=>'required',
        ]);

        if ($validator->fails())
        {
            return response()->json($validator->errors()->toJson(),400);
        }

        $truckNumber = $request->input('truck_number');

        $truck = Truck::where('number', $truckNumber)->first();


      if (!$truck) {
          return response()->json(['message' => 'Truck not found'], 404);
      }

      $trips = DB::table('trips')
          ->select( 'number', 'date','driver_id')
          ->where('truck_id', $truck->id)
          ->get();

      $driverIds = $trips->pluck('driver_id')->unique();
      $drivers = DB::table('drivers')
          ->select('id', 'name')
          ->whereIn('id', $driverIds)
          ->get();

      $truck->trips = $trips;
      $truck->drivers = $drivers;

      return response()->json(['truck information' => $truck]);

  

    }

    public function  GetTrucks ()
    {
      $trucks=Truck::all();
      return response()->json(['truck information' => $trucks]);
    }

    public function GetTruckRecord(Request $request)
    {
        $validator =Validator::make($request->all(),[
            'desk'=>'required',
        ]);

        if ($validator->fails())
        {
            return response()->json($validator->errors()->toJson(),400);
        }

        $desk = $request->input('desk');
        $branch = Branch::where('desk', $desk)->first();
    
        if (!$branch) {
            return response()->json([
                'message' => 'Branch not found',
            ], 404);
        }
    
        $trucks = $branch->trucks;
    
        return response()->json($trucks);
    }
}
