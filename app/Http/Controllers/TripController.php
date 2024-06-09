<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Manifest;
use App\Models\Permission;
use App\Models\Employee;
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
    // public function GetAllTrips()
    // {
    //   $trips=Trip::paginate(10);
    //   if($trips){
    //     return response()->json([
    //       'success' => true ,
    //       'data' => $trips
    //     ] , 200);

    //   }
    //   return response()->json([
    //     'success' => false ,
    //     'message' => 'no trips found'
    //   ] , 404);

    // }
    public function GetAllTrips()
    {
        try {
            // Paginate the trips
            $trips = Trip::paginate(10);
    
            if ($trips->isNotEmpty()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Trips retrieved successfully',
                    'data' => $trips
                ], 200);
            }
    
            return response()->json([
                'success' => false,
                'message' => 'No trips found'
            ], 404);
    
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while retrieving the trips',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    

//     public function AddTrip(Request $request)
//     {
//         $validator =  $request->validate([
//             'branch_id'=>'required',
//             'destination_id'=>'required',
//             'truck_id'=>'required',
//             'driver_id'=>'required',
//           //  'trip_number'=>'required|string',
    
//         ]);
//         $branch = Branch::findOrFail($validator['branch_id']);
//         $tripCount = Trip::where('branch_id',$branch->id)->count();
//          $tripNumber = strtoupper(substr($branch->desk, 0, 2)) . '_' . $branch->id . '_' . $tripCount;
      

//          $loggedInEmployee = Auth::guard('employee')->user();

//          // Check if the logged-in employee has the "add_trip" permission
//          $hasAddTripPermission = Permission::where([
//              ['employee_id', $loggedInEmployee->id],
//              ['add_trip', 1]
//          ])->exists();
     
//          if ($hasAddTripPermission) {  
//          $trip = new Trip();
//         $trip->branch_id = $validator['branch_id'];
//         $trip->destination_id = $validator['destination_id'];
//         $trip->truck_id = $validator['truck_id'];
//         $trip->driver_id = $validator['driver_id'];
//         $trip->number = $tripNumber;
//         $trip->date =now()->format('Y-m-d');
//         $trip-> created_by= Auth::guard('employee')->user()->name;
//           $trip->save();
        
       
//        $manifest = new Manifest();
//        $manifest->number = $tripNumber;
//       $manifest->trip_id =$trip->id;
//          $manifest->save();

//            return response()->json(['message'=>'trip  and manifest addedd successfully', ],200);
//     }
//     else
//     return response()->json(['error' => 'You do not have permission to add a trip'], 403);
//   }
public function AddTrip(Request $request)
{
    try {
        // Validate the request data
        $validator = Validator::make($request->all(), [
            'branch_id' => 'required|numeric',
            'destination_id' => 'required|numeric',
            'truck_id' => 'required|numeric',
            'driver_id' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->toJson()
            ], 400);
        }

        // Find the branch
        $branch = Branch::findOrFail($request->branch_id);

        // Generate the trip number
        $tripCount = Trip::where('branch_id', $branch->id)->count();
        $tripNumber = strtoupper(substr($branch->desk, 0, 2)) . '_' . $branch->id . '_' . ($tripCount + 1);

        // Get the logged-in employee
        $loggedInEmployee = Auth::guard('employee')->user();

        // Check if the logged-in employee has the "add_trip" permission
        $hasAddTripPermission = Permission::where([
            ['employee_id', $loggedInEmployee->id],
            ['add_trip', 1]
        ])->exists();

        if (!$hasAddTripPermission) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to add a trip'
            ], 403);
        }

        // Create the trip without the manifest ID first
        $trip = new Trip();
        $trip->branch_id = $request->branch_id;
        $trip->destination_id = $request->destination_id;
        $trip->truck_id = $request->truck_id;
        $trip->driver_id = $request->driver_id;
        $trip->number = $tripNumber;
        $trip->date = now()->format('Y-m-d');
        $trip->created_by = $loggedInEmployee->name;
        $trip->manifest_id = null; // Ensure manifest_id is set to null initially
        $trip->save();

        // Create the manifest and associate it with the trip
        $manifest = new Manifest();
        $manifest->number = $tripNumber;
        $manifest->trip_id = $trip->id;
        $manifest->save();

        // Update the trip with the manifest ID
        $trip->manifest_id = $manifest->id;
        $trip->save();

        return response()->json([
            'success' => true,
            'message' => 'Trip and manifest added successfully',
            'data' => $trip
        ], 201);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'An error occurred while adding the trip',
            'error' => $e->getMessage()
        ], 500);
    }
}





    // public function EditTrip(Request $request)
    // {

    //     $user = Auth::guard('employee')->user();
   
     
    //     $validator =Validator::make($request->all(),[
    //         'trip_id'=>'required',
    //         'branch_id'=>'numeric',
    //         'truck_id'=>'numeric',
    //         'driver_id'=>'numeric',
    //         'manifest_id'=>'numeric',
    //         'trip_number'=>'string',
    //         'source'=>'string',
    //         'destination'=>'string',
    //        'arrival_date'=>'date ',
    //        'status' => ['required',Rule::in(['active', 'closed', 'temporary'])  ],
    //   ]);
    //   if ($validator->fails())
    //   {
    //       return response()->json($validator->errors()->toJson(),400);
    //   }

    // //   $arrival_date=$request->arrival_date;
    // $loggedInEmployee = Auth::guard('employee')->user();

    // // Check if the logged-in employee has the "add_trip" permission
    // $hasAddTripPermission = Permission::where([
    //     ['employee_id', $loggedInEmployee->id],
    //     ['edit_trip', 1]
    // ])->exists();

    // if ($hasAddTripPermission) {  
  
    // $trip = Trip::find($request->trip_id);
    //     $updatedtrip= $trip->update(array_merge($request->all() ,[
    //     'edited_by' => $user->name,
    //     'editing_date' => now()->format('Y-m-d')
    //     ]
    //   ));
  

  
    //     return response()->json(['message' => 'trip edited successfully']);
  
    //  } else
    //     return response()->json(['error' => 'You do not have permission to edit a trip'], 403);
    //   }
    
    public function EditTrip(Request $request)
    {
        try {
            // Get the logged-in employee
            $user = Auth::guard('employee')->user();
    
            // Validate the request data
            $validator = Validator::make($request->all(), [
                'trip_id' => 'required|numeric',
                'branch_id' => 'numeric|nullable',
                'truck_id' => 'numeric|nullable',
                'driver_id' => 'numeric|nullable',
                'manifest_id' => 'numeric|nullable',
                'trip_number' => 'string|nullable',
                'source' => 'string|nullable',
                'destination' => 'string|nullable',
                'arrival_date' => 'date|nullable',
                'status' => ['required', Rule::in(['active', 'closed', 'temporary'])]
            ]);
    
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->toJson()
                ], 400);
            }
    
            // Check if the logged-in employee has the "edit_trip" permission
            $hasEditTripPermission = Permission::where([
                ['employee_id', $user->id],
                ['edit_trip', 1]
            ])->exists();
    
            if (!$hasEditTripPermission) {
                return response()->json([
                    'success' => false,
                    'message' => 'You do not have permission to edit a trip'
                ], 403);
            }
    
            // Find the trip by ID
            $trip = Trip::find($request->trip_id);
    
            if (!$trip) {
                return response()->json([
                    'success' => false,
                    'message' => 'Trip not found'
                ], 404);
            }
    
            // Update the trip details
            $trip->update(array_merge($validator->validated(), [
                'edited_by' => $user->name,
                'editing_date' => now()->format('Y-m-d')
            ]));
    
            return response()->json([
                'success' => true,
                'message' => 'Trip edited successfully'
            ], 200);
    
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while editing the trip',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    // public function CancelTrip(Request $request)
    // {
       
        
    //       $validator =Validator::make($request->all(),[
    //         'trip_id'=>'required',
    //     ]);
    
    //     if ($validator->fails())
    //     {
    //         return response()->json($validator->errors()->toJson(),400);
    //     }
    //     $loggedInEmployee = Auth::guard('employee')->user();

    //     // Check if the logged-in employee has the "add_trip" permission
    //     $hasAddTripPermission = Permission::where([
    //         ['employee_id', $loggedInEmployee->id],
    //         ['edit_trip', 1]
    //     ])->exists();
    
    //     if ($hasAddTripPermission) {  
    //         $trip = Trip::find($request->trip_id)->delete();
    
    //         return response()->json(['msg'=>'trip has been canceled'], 200) ;
        
    //       } else
    //       return response()->json(['error' => 'You do not have permission to delete a trip'], 403);
    //     }
    public function CancelTrip(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'trip_id' => 'required|numeric',
            ]);
    
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->toJson()
                ], 400);
            }
    
            $loggedInEmployee = Auth::guard('employee')->user();
    
            // Check if the logged-in employee has the "edit_trip" permission
            $hasEditTripPermission = Permission::where([
                ['employee_id', $loggedInEmployee->id],
                ['edit_trip', 1]
            ])->exists();
    
            if (!$hasEditTripPermission) {
                return response()->json([
                    'success' => false,
                    'message' => 'You do not have permission to delete a trip'
                ], 403);
            }
                $trip = Trip::find($request->trip_id);
    
            if (!$trip) {
                return response()->json([
                    'success' => false,
                    'message' => 'Trip not found'
                ], 404);
            }
    
            $trip->delete();
    
            return response()->json([
                'success' => true,
                'message' => 'Trip has been canceled'
            ], 200);
    
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while canceling the trip',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    // public function GetActiveTrips()
    // {
       
    //     $trips = Trip::with('driver:id,name', 'branch:id,address,desk', 'truck:id,number')
    //     ->where('status', 'active')
    //     ->paginate(10);

        
    //     if ($trips->isEmpty()) {
    //         return response()->json([
    //           'success' => false ,
    //           'message' => 'No active trips found'
    //         ] , 404);
    //     }

    //     return response()->json([
    //       'success' => true ,
    //       'data' => $trips]);
    // }
    public function GetActiveTrips()
    {
        try {
            // Retrieve active trips with related data
            $trips = Trip::with('driver:id,name', 'branch:id,address,desk', 'truck:id,number')
                ->where('status', 'active')
                ->paginate(10);
    
            if ($trips->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No active trips found'
                ], 404);
            }
    
            return response()->json([
                'success' => true,
                'message' => 'Active trips retrieved successfully',
                'data' => $trips
            ], 200);
    
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while retrieving active trips',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
//     public function ArchiveData(Request $request)
//     {
     
//    $validator =Validator::make($request->all(),[
//      'trip_id'=>'required',
//         ]);

//  if ($validator->fails())
//  {
//      return response()->json($validator->errors()->toJson(),400);
//  }

//       $record = Trip::findOrFail($request->trip_id);
//       $record->archived = true;
//       $record->save();


//       return response()->json(['msg'=>'trip has been archived'], 200) ;
 
//    }
public function ArchiveData(Request $request)
{
    try {
        $validator = Validator::make($request->all(), [
            'trip_id' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->toJson()
            ], 400);
        }

        $record = Trip::findOrFail($request->trip_id);
        $record->archived = true;
        $record->save();

        return response()->json([
            'success' => true,
            'message' => 'Trip has been archived'
        ], 200);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'An error occurred while archiving the trip',
            'error' => $e->getMessage()
        ], 500);
    }
}

  //  public function GetTripInformation( $trip_number)
  //  {
 

  //   $trip = Trip::where('number', $trip_number)->first();

    
  //   if (!$trip) {
  //       return response()->json([
  //         'success' => false ,
  //         'message' => 'Trip not found'
  //       ], 404);
  //   }

  //    return response()->json([
  //     'success' => true ,
  //     'data' => $trip 
  //     ] , 200) ;

  // }

  public function GetTripInformation($trip_number)
  {
      try {
          $trip = Trip::where('number', $trip_number)->first();
  
          if (!$trip) {
              return response()->json([
                  'success' => false,
                  'message' => 'Trip not found'
              ], 404);
          }
  
          return response()->json([
              'success' => true,
              'message' => 'Trip information retrieved successfully',
              'data' => $trip
          ], 200);
  
      } catch (\Exception $e) {
          return response()->json([
              'success' => false,
              'message' => 'An error occurred while retrieving the trip information',
              'error' => $e->getMessage()
          ], 500);
      }
  }
  


  //  public function GetArchiveData(Request $request)
  //  {

  //   $archivedRecords = Trip::with('driver:id,name', 'branch:id,address', 'truck:id,number')
  //   ->where('archived', true)->paginate(10);
    
    
  //   if ($archivedRecords->isEmpty())
  //    {
       
  //       return response()->json([
  //         'success' => false ,
  //         'message' => 'No archive trips found'
  //       ] , 404);
  //    }

  //   return response()->json([
  //     'success' => true ,
  //     'data' => $archivedRecords]);


  // }
  public function GetArchiveData(Request $request)
  {
      try {
          $archivedRecords = Trip::with('driver:id,name', 'branch:id,address', 'truck:id,number')
              ->where('archived', true)
              ->paginate(10);
  
          if ($archivedRecords->isEmpty()) {
              return response()->json([
                  'success' => false,
                  'message' => 'No archived trips found'
              ], 404);
          }
  
          return response()->json([
              'success' => true,
              'message' => 'Archived trips retrieved successfully',
              'data' => $archivedRecords
          ], 200);
  
      } catch (\Exception $e) {
          return response()->json([
              'success' => false,
              'message' => 'An error occurred while retrieving archived trips',
              'error' => $e->getMessage()
          ], 500);
      }
  }
  
}



