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
      return response()->json($trips);

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
        // $tripNumber = strtoupper(substr($branch->desk, 0, 2)) . '_' . $tripCount + 1;
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
        ->paginate(3);

        
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

   public function GetTripInformation( $trip_number)
   {
 

    $trip = Trip::where('number', $trip_number)->first();

    
    if (!$trip) {
        return response()->json(['message' => 'Trip not found'], 404);
    }


     return response()->json(['trip  information:'=>$trip]) ;
   //  return response()->json(['truck information' => $truck]);
  }




   public function GetArchiveData(Request $request)
   {

    $archivedRecords = Trip::with('driver:id,name', 'branch:id,address', 'truck:id,number')
    ->where('archived', true)->paginate(3);
    
    
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
   // 'manifest_id'=>'',
    'number'=>'required',
    'sender'=>'required',
    'receiver'=> 'required',
    'sender_number'=> 'required|max:15',
    'receiver_number'=> 'required|max:15',
    'num_of_packages'=>'required',
    'type_id'=>'required',
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

   $price = Price::findOrFail($request->type_id);

   $shippingCost = $price->cost * $request->weight;


    $trip=Shipping::create([
        'source_id'=>$request->source_id,
        'destination_id'=>$request->destination_id,
      //  'manifest_id'=>$request->manifest_id,
        'number'=>$request->number,
        'sender'=>$request->sender,
        'receiver'=>$request->receiver,
        'sender_number'=>$request->sender_number,
        'receiver_number'=>$request->receiver_number,
        'num_of_packages'=> $request->num_of_packages,
        'price_id'=>$request->type_id,
        'weight'=>$request->weight,
        'size'=>$request->size,
        'content'=>$request->content,
        'marks'=>$request->marks,
        'notes'=>$request->notes,
        'shipping_cost'=>$shippingCost,
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



