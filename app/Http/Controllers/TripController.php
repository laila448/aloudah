<?php

namespace App\Http\Controllers;

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
      $trips=Trip::all();
      return response()->json($trips);

    }


    public function AddTrip(Request $request)
    {

        $validator =Validator::make($request->all(),[
            'branch_id'=>'required',
            'destination_id'=>'required',
            'truck_id'=>'required',
            'driver_id'=>'required',
            'manifest_id'=> 'required',
            'trip_number'=>'required|string',
        //    'source'=>'required|string',
          
           
        ]);
        if ($validator->fails())
        {
            return response()->json($validator->errors()->toJson(),400);
        }

        $employee = Auth::guard('employee')->user()->name;
            $trip=Trip::create([
                'branch_id'=>$request->branch_id,
                'destination_id'=>$request->destination_id,
                'truck_id'=>$request->truck_id,
                'driver_id'=>$request->driver_id,
                'manifest_id'=>$request->manifest_id,
                'number'=>$request->trip_number,
                'source'=> $request->source,
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

    public function GetActiveTrips()
    {
       
        $trips = Trip::with('driver:id,name', 'branch:id,address,desk', 'truck:id,number')
        ->where('status', 'active')
        ->get();

        
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

   public function GetTripInformation(Request $request)
   {
    
  $validator =Validator::make($request->all(),[
    'trip_number'=>'required',
       ]);


    $tripnum = $request->input('trip_number');

    $trip = Trip::where('number', $tripnum)->first();

    
    if (!$trip) {
        return response()->json(['message' => 'Trip not found'], 404);
    }


     return response()->json(['trip  information:'=>$trip]) ;
   //  return response()->json(['truck information' => $truck]);
  }




   public function GetArchiveData(Request $request)
   {

    $archivedRecords = Trip::with('driver:id,name', 'branch:id,address', 'truck:id,number')
    ->where('archived', true)->get();
    
    
    if ($archivedRecords->isEmpty())
     {
       
        return response()->json(['message' => 'No archive trips found']);
     }

    return response()->json(['Archived trips' => $archivedRecords]);


  }
    public function AddTripInvoice(Request $request) 
  { 
    $validator =Validator::make($request->all(),[
    'source_id'=>'required',
    'destination_id' => 'required',
    'number'=>'required',
    'sender'=>'required',
    'receiver'=> 'required',
    'sender_number'=> 'required|max:15',
    'receiver_number'=> 'required|max:15',
    'num_of_packages'=>'required',
    'package_type'=>'required',
    'weight'=>'required',
    'size'=>'required',
     'content'=>'required',
     'marks'=>'required',
    'notes'=>'string',
     'shipping_cost',
     'against_shipping',
     'adapter',
     'advance',
     'miscellaneous',
     'prepaid',
     'discount',
     'collection',
   
    ]);
    if ($validator->fails())
    {
        $errors = $validator->errors();
   }

  // $employee = Auth::guard('employee')->user()->name;

    $trip=Shipping::create([
        'source_id'=>$request->source_id,
        'destination_id'=>$request->destination_id,
        'number'=>$request->number,
        'sender'=>$request->sender,
        'receiver'=>$request->receiver,
        'sender_number'=>$request->sender_number,
        'receiver_number'=>$request->receiver_number,
        'num_of_packages'=> $request->num_of_packages,
        'type'=>$request->package_type,
        'weight'=>$request->weight,
        'size'=>$request->size,
        'content'=>$request->content,
        'marks'=>$request->marks,
        'notes'=>$request->notes,
        'shipping_cost'=>$request->shipping_cost,
        'against_shipping'=>$request->against_shipping,
        'adapter'=>$request->adapter,
        'advance'=>$request->advance,
        'miscellaneous'=>$request->miscellaneous,
        'prepaid'=>$request->prepaid,
        'discount'=>$request->discount,
        'collection'=>$request->collection,

      

   ]);
   return response()->json(['message'=>' Addedd Successfully', ],200);

}
    
}



