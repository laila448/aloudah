<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Manifest;
use App\Models\Price;
use App\Models\Shipingsss;
use App\Models\Shipping;
use App\Models\Trip;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class TripController extends Controller
{
    public function GetAllTrips()
    {
      $trips=Trip::paginate(10);
      if($trips){
        return response()->json([
          'success' => true ,
          'data' => $trips
        ] , 200);

      }
      return response()->json([
        'success' => false ,
        'message' => 'no trips found'
      ] , 404);

    }


    public function AddTrip(Request $request)
    {

        $validator =  $request->validate([
            'branch_id'=>'required',
            'destination_id'=>'required',
            'truck_id'=>'required',
            'driver_id'=>'required',
          //  'trip_number'=>'required|string',
    
        ]);
        $branch = Branch::findOrFail($validator['branch_id']);
        $tripCount = Trip::where('branch_id',$branch->id)->count();
         $tripNumber = strtoupper(substr($branch->desk, 0, 2)) . '_' . $branch->id . '_' . $tripCount;
      
         $trip = new Trip();
        $trip->branch_id = $validator['branch_id'];
        $trip->destination_id = $validator['destination_id'];
        $trip->truck_id = $validator['truck_id'];
        $trip->driver_id = $validator['driver_id'];
        $trip->number = $tripNumber;
        $trip->date =now()->format('Y-m-d');
        $trip-> created_by= Auth::guard('employee')->user()->name;
          $trip->save();
        
       


    

       $manifest = new Manifest();
       $manifest->number = $tripNumber;
      $manifest->trip_id =$trip->id;
         $manifest->save();



           return response()->json(['message'=>'trip  and manifest addedd successfully', ],200);
    
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

    public function GetActiveTrips()
    {
       
        $trips = Trip::with('driver:id,name', 'branch:id,address,desk', 'truck:id,number')
        ->where('status', 'active')
        ->paginate(10);

        
        if ($trips->isEmpty()) {
            return response()->json([
              'success' => false ,
              'message' => 'No active trips found'
            ] , 404);
        }

        return response()->json([
          'success' => true ,
          'data' => $trips]);
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

   public function GetTripInformation( $trip_number)
   {
 

    $trip = Trip::where('number', $trip_number)->first();

    
    if (!$trip) {
        return response()->json([
          'success' => false ,
          'message' => 'Trip not found'
        ], 404);
    }

     return response()->json([
      'success' => true ,
      'data' => $trip 
      ] , 200) ;

  }




   public function GetArchiveData(Request $request)
   {

    $archivedRecords = Trip::with('driver:id,name', 'branch:id,address', 'truck:id,number')
    ->where('archived', true)->paginate(10);
    
    
    if ($archivedRecords->isEmpty())
     {
       
        return response()->json([
          'success' => false ,
          'message' => 'No archive trips found'
        ] , 404);
     }

    return response()->json([
      'success' => true ,
      'data' => $archivedRecords]);


  }
    
}



