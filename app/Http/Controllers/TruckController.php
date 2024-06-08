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
    // public function AddTruck(Request $request){

    //     $validator =Validator::make($request->all(),[
    //         'branch_id'=>'required',
    //         'number'=>'required|min:4|max:20',
    //         'line'=>'string|required',
    //         'notes'=>'string',
    //     ]);
    //         $createdby = Auth::guard('branch_manager')->user()->name;
    //         $truck=Truck::create([
    //             'branch_id'=>$request->branch_id,
    //             'number'=>$request->number,
    //             'line'=> $request->line,
    //             'notes'=>$request->notes,
    //             'created_by'=>$createdby,
    //             'adding_data'=>now()->format('Y-m-d'),
                
    //         ]);
          
    //         return response()->json([
    //             'message'=>'Truck addedd successfully',
    //         ],201);
    //     }
    public function AddTruck(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'branch_id' => 'required|numeric',
                'number' => 'required|min:4|max:20|string',
                'line' => 'required|string',
                'notes' => 'string|nullable',
            ]);
    
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->toJson()
                ], 400);
            }
    
            $createdby = Auth::guard('branch_manager')->user()->name;
    
            $truck = Truck::create([
                'branch_id' => $request->branch_id,
                'number' => $request->number,
                'line' => $request->line,
                'notes' => $request->notes,
                'created_by' => $createdby,
                'adding_data' => now()->format('Y-m-d'),
            ]);
    
            return response()->json([
                'success' => true,
                'message' => 'Truck added successfully',
                'data' => $truck
            ], 200);
    
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while adding the truck',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    

    // public function UpdateTruck(Request $request){

    //         $validator =Validator::make($request->all(),[
    //             'truck_id'=>'required',
    //             'number'=>'min:4|max:20',
    //             'line'=>'string',
    //             'notes'=>'string',
    //         ]);
    
    //         if ($validator->fails())
    //         {
    //             return response()->json($validator->errors()->toJson(),400);
    //         }
          
          
    //         $edit_by = Auth::guard('branch_manager')->user()->name;
             
    //         $truck = Truck::where('id' , $request->truck_id)->first();
    
    //         if($truck){
    
    //             $updated_truck = $truck->update( array_merge(
    //                 $validator->validated(),
    //                 ['editing_by'=>$edit_by,
    //                 'editing_date'=>now()->format('Y-m-d'),
    //                 ]
    //             ));
    
    //         }
          
    //         return response()->json([
    //             'message'=>'Truck updated successfully',
    //         ],201);
        
           
    // }
    public function UpdateTruck(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'truck_id' => 'required|numeric',
                'number' => 'min:4|max:20|string|nullable',
                'line' => 'string|nullable',
                'notes' => 'string|nullable',
            ]);
    
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->toJson()
                ], 400);
            }
    
            $truck = Truck::where('id', $request->truck_id)->first();
    
            if (!$truck) {
                return response()->json([
                    'success' => false,
                    'message' => 'Truck not found'
                ], 404);
            }
    
            $edit_by = Auth::guard('branch_manager')->user()->name;
    
            $truck->update(array_merge(
                $validator->validated(),
                [
                    'editing_by' => $edit_by,
                    'editing_date' => now()->format('Y-m-d'),
                ]
            ));
    
            return response()->json([
                'success' => true,
                'message' => 'Truck updated successfully'
            ], 200);
    
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating the truck',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    // public function DeleteTruck(Request $request){

    //     $validator =Validator::make($request->all(),[
    //         'truck_id'=>'required',
    //     ]);

    //     if ($validator->fails())
    //     {
    //         return response()->json($validator->errors()->toJson(),400);
    //     }


    //     $truck = Truck::find($request->truck_id)->delete();
      
    //     return response()->json([
    //         'message'=>'Truck deleted successfully',
    //     ],201);
    // }

    public function DeleteTruck(Request $request)
{
    try {
        $validator = Validator::make($request->all(), [
            'truck_id' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->toJson()
            ], 400);
        }

        $truck = Truck::find($request->truck_id);

        if (!$truck) {
            return response()->json([
                'success' => false,
                'message' => 'Truck not found'
            ], 404);
        }

        $truck->delete();

        return response()->json([
            'success' => true,
            'message' => 'Truck deleted successfully'
        ], 200);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'An error occurred while deleting the truck',
            'error' => $e->getMessage()
        ], 500);
    }
}


    //   public function  GetTruckInformation( $truck_number)
    // {
     
    //     $truck = Truck::where('number', $truck_number)->first();


    //   if (!$truck) {
    //       return response()->json([
    //         'success' => false ,
    //         'message' => 'Truck not found'
    //     ], 404);
    //   }

    //   $trips = DB::table('trips')
    //       ->select( 'number', 'date','driver_id')
    //       ->where('truck_id', $truck->id)
    //       ->get();

    //   $driverIds = $trips->pluck('driver_id')->unique();
    //   $drivers = DB::table('drivers')
    //       ->select('id', 'name')
    //       ->whereIn('id', $driverIds)
    //       ->get();

    //   $truck->trips = $trips;
    //   $truck->drivers = $drivers;

    //   return response()->json([
    //     'success' => true ,
    //     'data' => $truck
    //     ], 200);

    // }
    public function GetTruckInformation($truck_number)
    {
        try {
            // Find the truck by number
            $truck = Truck::where('number', $truck_number)->first();
    
            if (!$truck) {
                return response()->json([
                    'success' => false,
                    'message' => 'Truck not found'
                ], 404);
            }
    
            $trips = DB::table('trips')
                ->select('number', 'date', 'driver_id')
                ->where('truck_id', $truck->id)
                ->get();
    
            $driverIds = $trips->pluck('driver_id')->unique();
    
            $drivers = DB::table('drivers')
                ->select('id', 'name')
                ->whereIn('id', $driverIds)
                ->get();
    
            $truck->trips = $trips;
            $truck->drivers = $drivers;
    
            return response()->json([
                'success' => true,
                'message' => 'Truck information retrieved successfully',
                'data' => $truck
            ], 200);
    
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while retrieving the truck information',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    // public function  GetTrucks ()
    // {
    //   $trucks=Truck::pagiate(10);
    //   if($trucks){
    //     return response()->json([
    //         'success' => true ,
    //         'data' => $trucks
    //     ] , 200);
    //   }
    //   return response()->json([
    //     'success' => false ,
    //     'message' => 'no trucks found' 
    //     ] , 404);
    // }
    public function GetTrucks()
    {
        try {
            $trucks = Truck::paginate(10);
            if ($trucks->isNotEmpty()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Trucks retrieved successfully',
                    'data' => $trucks
                ], 200);
            }
    
            return response()->json([
                'success' => false,
                'message' => 'No trucks found'
            ], 404);
    
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while retrieving the trucks',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    // public function GetTruckRecord($desk)
    // {
  
    //     $branch = Branch::where('desk', $desk)->first();
    
    //     if (!$branch) {
    //         return response()->json([
    //             'success' => false ,
    //             'message' => 'Branch not found',
    //         ], 404);
    //     }
    
    //     $trucks = $branch->trucks;
    
    //     return response()->json([
    //         'success' => true ,
    //         'data' => $trucks
    //     ] , 200);
    // }
    public function GetTruckRecord($desk)
{
    try {
        $branch = Branch::where('desk', $desk)->first();

        if (!$branch) {
            return response()->json([
                'success' => false,
                'message' => 'Branch not found'
            ], 404);
        }
        $trucks = $branch->trucks;
        return response()->json([
            'success' => true,
            'message' => 'Trucks retrieved successfully',
            'data' => $trucks
        ], 200);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'An error occurred while retrieving the trucks',
            'error' => $e->getMessage()
        ], 500);
    }
}

}
