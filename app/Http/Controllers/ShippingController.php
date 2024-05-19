<?php

namespace App\Http\Controllers;

use App\Models\Manifest;
use App\Models\Price;
use App\Models\Shipping;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Namshi\JOSE\Signer\OpenSSL\RSA;

class ShippingController extends Controller
{
    public function DetermineShippingPrices(Request $request)
    {


        $validator = Validator::make($request->all(),[
            'type'=>'required',
            'cost'=>'required',
            
        ]);

        if ($validator->fails())
        {
            return response()->json($validator->errors()->toJson(),400);
        }
        $prices=Price::Create([
                'type'=>$request->type,
                'cost'=>$request->cost,

        ]);

        return response()->json([
            'message'=>'addedd successfully',
        ],201);



    }


public function EditShippingPrices(Request $request)
{
    $validator = Validator::make($request->all(),[
        'type'=>'required',
        'cost'=>'required',
        
    ]);

    if ($validator->fails())
    {
        return response()->json($validator->errors()->toJson(),400);
    }
    $price = Price::where('type' , $request->type)->first();
    
       if($price){
    
                $updated_price = $price->update( array_merge(
                    $validator->validated(),
                    
                ));
    
              }
          
              
          else {
               return response()->json(['message' => 'type not found'], 404);
                }



            return response()->json([
                'message'=>' edited successfully',
            ],201);


}

public function GetPricesList()
{
    $list=Price::paginate(10);
    return response()->json(['Price List :' =>$list]);


}
public function GetAllRceipts($destination_id)
{
    $branch_id= Auth::guard('employee')->user()->branch_id;
    
    $receipts=Shipping::where ([
        ['source_id', '=', $branch_id],
        ['destination_id', '=', $destination_id],
                       ])->get();
    
    //    where('destination_id' , $destination_id)->get();paginate(10)
    
    return response()->json(['All Rceipts :' =>$receipts]);



}

public function getManifestWithInvoices($manifestNumber)
{
    $manifest = Manifest::with('shippings')->where('number', $manifestNumber)->first();

    if (!$manifest) {
        return response()->json([
            'message' => 'Manifest not found',
        ], 404);
    }

    return response()->json([
        'manifest' => $manifest,
    ], 200);
}

public function AddInvoice(Request $request) 
  { 
    $validator =Validator::make($request->all(),[
    'source_id'=>'required',
    'destination_id' => 'required',
     'manifest_number'=>'required',
   //'number'=>'required',
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
  // $discountedShippingCost = $shippingCost * (1 - ($request->discount / 100));

    $shipping=Shipping::create([
        'source_id'=>$request->source_id,
        'destination_id'=>$request->destination_id,
       'manifest_number'=>$request->manifest_number,
       // 'number'=>$request->number,
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

   $shipping->number = $shipping->id;
   $shipping->save();





   // Update the Manifest general_total
   $manifest = Manifest::where('number', $request->manifest_number)
       ->first();

   if ($manifest) {
       $manifest->general_total += $shippingCost;
       $manifest->save();

   }

   return response()->json(['message'=>' Addedd Successfully', ],200);

}


public function UpdateManifest(Request $request)
{

    $validator =Validator::make($request->all(),[
         'manifest_id'=>'required',
         'status'=>Rule::in(['open', 'closed', 'temporary']),
         'misc_paid'=>'',
         'shipping_cost'=>'',
         'against_shipping'=>'',
         'adapter'=>'',
         'advance'=>'',
         'discount'=>'',
         'collection'=>'',
       
        ]);
        if ($validator->fails())
        {
            $errors = $validator->errors();
       }
       $manifest = Manifest::find($request->manifest_id);
      $updatedmanifest= $manifest->update(array_merge($request->all() 
    ));
    
    


    if ($request->has('discount') && $request->discount !== null) {
        $manifest->net_total = $manifest->general_total * (1 - ($request->discount / 100));
        $manifest->save();
    }

    
    return response()->json(['message'=>' updated Successfully', ],200);
}
}
