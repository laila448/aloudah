<?php

namespace App\Http\Controllers;

use App\Models\Manifest;
use App\Models\Permission;
use App\Models\Price;
use App\Models\Shipping;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ShippingController extends Controller
{
    // public function DetermineShippingPrices(Request $request)
    // {


    //     $validator = Validator::make($request->all(),[
    //         'type'=>'required',
    //         'cost'=>'required',
            
    //     ]);

    //     if ($validator->fails())
    //     {
    //         return response()->json($validator->errors()->toJson(),400);
    //     }
    //     $prices=Price::Create([
    //             'type'=>$request->type,
    //             'cost'=>$request->cost,

    //     ]);

    //     return response()->json([
    //         'message'=>'addedd successfully',
    //     ],201);



    // }
    public function DetermineShippingPrices(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'type' => 'required|string',
                'cost' => 'required|numeric',
            ]);
    
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->toJson()
                ], 400);
            }
    
            // Create the new price entry
            $price = Price::create([
                'type' => $request->type,
                'cost' => $request->cost,
            ]);
    
            return response()->json([
                'success' => true,
                'message' => 'Price added successfully',
                'data' => $price
            ], 200);
    
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while adding the price',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    

// public function EditShippingPrices(Request $request)
// {
//     $validator = Validator::make($request->all(),[
//         'type'=>'required',
//         'cost'=>'required',
        
//     ]);

//     if ($validator->fails())
//     {
//         return response()->json($validator->errors()->toJson(),400);
//     }
//     $price = Price::where('type' , $request->type)->first();
    
//        if($price){
    
//                 $updated_price = $price->update( array_merge(
//                     $validator->validated(),
                    
//                 ));
    
//               }
          
              
//           else {
//                return response()->json(['message' => 'type not found'], 404);
//                 }



//             return response()->json([
//                 'message'=>' edited successfully',
//             ],201);


// }
public function EditShippingPrices(Request $request)
{
    try {
        // Validate the request data
        $validator = Validator::make($request->all(), [
            'type' => 'required|string',
            'cost' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->toJson()
            ], 400);
        }

        // Find the price by type
        $price = Price::where('type', $request->type)->first();

        if (!$price) {
            return response()->json([
                'success' => false,
                'message' => 'Type not found'
            ], 404);
        }

        // Update the price details
        $price->update($validator->validated());

        return response()->json([
            'success' => true,
            'message' => 'Price edited successfully',
            'data' => $price
        ], 200);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'An error occurred while editing the price',
            'error' => $e->getMessage()
        ], 500);
    }
}

// public function GetPricesList()
// {
//     $list=Price::paginate(10);
//     return response()->json(['Price List :' =>$list]);


// }

public function GetPricesList()
{
    try {
        // Paginate the prices list
        $list = Price::paginate(10);

        if ($list->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No prices found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Prices retrieved successfully',
            'data' => $list
        ], 200);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'An error occurred while retrieving the prices',
            'error' => $e->getMessage()
        ], 500);
    }
}

// public function GetAllRceipts($destination_id)
// {
//     $branch_id= Auth::guard('employee')->user()->branch_id;
    
//     $receipts=Shipping::where ([
//         ['source_id', '=', $branch_id],
//         ['destination_id', '=', $destination_id],
//                        ])->get();
    
//     //    where('destination_id' , $destination_id)->get();paginate(10)
    
//     return response()->json(['All Rceipts :' =>$receipts]);



// }
public function GetAllRceipts($destination_id)
{
    try {
        $branch_id = Auth::guard('employee')->user()->branch_id;

        $receipts = Shipping::where([
            ['source_id', '=', $branch_id],
            ['destination_id', '=', $destination_id],
        ])->get();

        if ($receipts->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No receipts found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Receipts retrieved successfully',
            'data' => $receipts
        ], 200);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'An error occurred while retrieving the receipts',
            'error' => $e->getMessage()
        ], 500);
    }
}

// public function getManifestWithInvoices($manifestNumber)
// {
//     $manifest = Manifest::with('shippings')->where('number', $manifestNumber)->first();

//     if (!$manifest) {
//         return response()->json([
//             'message' => 'Manifest not found',
//         ], 404);
//     }

//     return response()->json([
//         'manifest' => $manifest,
//     ], 200);
// }
public function getManifestWithInvoices($manifestNumber)
{
    try {
        $manifest = Manifest::with('shippings')->where('number', $manifestNumber)->first();

        if (!$manifest) {
            return response()->json([
                'success' => false,
                'message' => 'Manifest not found',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Manifest retrieved successfully',
            'data' => $manifest,
        ], 200);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'An error occurred while retrieving the manifest',
            'error' => $e->getMessage()
        ], 500);
    }
}

// public function AddInvoice(Request $request) 
//   { 
//     $validator =Validator::make($request->all(),[
//     'source_id'=>'required',
//     'destination_id' => 'required',
//      'manifest_number'=>'required',
//    //'number'=>'required',
//     'sender'=>'required',
//     'receiver'=> 'required',
//     'sender_number'=> 'required|max:15',
//     'receiver_number'=> 'required|max:15',
//     'num_of_packages'=>'required',
//     'type_id'=>'required',
//     'weight'=>'required',
//     'size'=>'required',
//      'content'=>'required',
//      'marks'=>'required',
//     'notes'=>'string',
//      'shipping_cost',
//      'against_shipping',
//      'adapter',
//      'advance',
//      'miscellaneous',
//      'prepaid',
//      'discount',
//      'collection',
   
//     ]);
//     if ($validator->fails())
//     {
//         $errors = $validator->errors();
//    }


//    $price = Price::findOrFail($request->type_id);

//    $shippingCost = $price->cost * $request->weight;
//   // $discountedShippingCost = $shippingCost * (1 - ($request->discount / 100));

//     $shipping=Shipping::create([
//         'source_id'=>$request->source_id,
//         'destination_id'=>$request->destination_id,
//        'manifest_number'=>$request->manifest_number,
//        // 'number'=>$request->number,
//         'sender'=>$request->sender,
//         'receiver'=>$request->receiver,
//         'sender_number'=>$request->sender_number,
//         'receiver_number'=>$request->receiver_number,
//         'num_of_packages'=> $request->num_of_packages,
//         'price_id'=>$request->type_id,
//         'weight'=>$request->weight,
//         'size'=>$request->size,
//         'content'=>$request->content,
//         'marks'=>$request->marks,
//         'notes'=>$request->notes,
//         'shipping_cost'=>$shippingCost,
//         'against_shipping'=>$request->against_shipping,
//         'adapter'=>$request->adapter,
//         'advance'=>$request->advance,
//         'miscellaneous'=>$request->miscellaneous,
//         'prepaid'=>$request->prepaid,
//         'discount'=>$request->discount,
//         'collection'=>$request->collection,

      

//    ]);

//    $shipping->number = $shipping->id;
//    $shipping->save();





//    // Update the Manifest general_total
//    $manifest = Manifest::where('number', $request->manifest_number)
//        ->first();

//    if ($manifest) {
//        $manifest->general_total += $shippingCost;
//        $manifest->save();

//    }

//    return response()->json(['message'=>' Addedd Successfully', ],200);

// }

public function AddInvoice(Request $request)
{
    try {
        $validator = Validator::make($request->all(), [
            'source_id' => 'required|numeric',
            'destination_id' => 'required|numeric',
            'manifest_number' => 'required|string',
            'sender' => 'required|string',
            'receiver' => 'required|string',
            'sender_number' => 'required|max:15',
            'receiver_number' => 'required|max:15',
            'num_of_packages' => 'required|numeric',
            'type_id' => 'required|numeric',
            'weight' => 'required|numeric',
            'size' => 'required|string',
            'content' => 'required|string',
            'marks' => 'required|string',
            'notes' => 'string|nullable',
            'shipping_cost' => 'numeric|nullable',
            'against_shipping' => 'numeric|nullable',
            'adapter' => 'numeric|nullable',
            'advance' => 'numeric|nullable',
            'miscellaneous' => 'numeric|nullable',
            'prepaid' => 'numeric|nullable',
            'discount' => 'numeric|nullable',
            'collection' => 'numeric|nullable',
            'quantity' => 'numeric|nullable'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->toJson()
            ], 400);
        }

        $price = Price::findOrFail($request->type_id);
        $shippingCost = $price->cost * $request->weight;

        $barcode = 'SHIP-' . uniqid() . Str::random(6);

        $shipping = Shipping::create([
            'source_id' => $request->source_id,
            'destination_id' => $request->destination_id,
            'manifest_number' => $request->manifest_number,
            'sender' => $request->sender,
            'receiver' => $request->receiver,
            'sender_number' => $request->sender_number,
            'receiver_number' => $request->receiver_number,
            'num_of_packages' => $request->num_of_packages,
            'price_id' => $request->type_id,
            'weight' => $request->weight,
            'size' => $request->size,
            'content' => $request->content,
            'marks' => $request->marks,
            'notes' => $request->notes,
            'shipping_cost' => $shippingCost,
            'against_shipping' => $request->against_shipping,
            'adapter' => $request->adapter,
            'advance' => $request->advance,
            'miscellaneous' => $request->miscellaneous,
            'prepaid' => $request->prepaid,
            'discount' => $request->discount,
            'collection' => $request->collection,
            'barcode' => $barcode,
            'quantity' => $request->quantity
        ]);

        $shipping->number = $shipping->id;
        $shipping->save();

        $manifest = Manifest::where('number', $request->manifest_number)->first();

        if ($manifest) {
            $manifest->general_total += $shippingCost;
            $manifest->save();
        }

        return response()->json([
            'success' => true,
            'message' => 'Invoice added successfully',
            'data' => $shipping
        ], 200);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'An error occurred while adding the invoice',
            'error' => $e->getMessage()
        ], 500);
    }
}

// public function UpdateManifest(Request $request)
// {

//     $validator =Validator::make($request->all(),[
//          'manifest_id'=>'required',
//          'status'=>Rule::in(['open', 'closed', 'temporary']),
//          'misc_paid'=>'',
//          'shipping_cost'=>'',
//          'against_shipping'=>'',
//          'adapter'=>'',
//          'advance'=>'',
//          'discount'=>'',
//          'collection'=>'',
       
//         ]);
//         if ($validator->fails())
//         {
//             $errors = $validator->errors();
//        }
//        $loggedInEmployee = Auth::guard('employee')->user();

//        // Check if the logged-in employee has the "add_trip" permission
//        $hasAddTripPermission = Permission::where([
//            ['employee_id', $loggedInEmployee->id],
//            ['edit_manifest', 1]
//        ])->exists();
//        if ($hasAddTripPermission) { 
//        $manifest = Manifest::find($request->manifest_id);
//       $updatedmanifest= $manifest->update(array_merge($request->all() 
//     ));
    
    


//     if ($request->has('discount') && $request->discount !== null) {
//         $manifest->net_total = $manifest->general_total * (1 - ($request->discount / 100));
//         $manifest->save();
//     }
// }

// else
// return response()->json(['error' => 'You do not have permission to edit a manifest'], 403);
// }

public function UpdateManifest(Request $request)
{
    try {
        // Validate the request data
        $validator = Validator::make($request->all(), [
            'manifest_id' => 'required|numeric',
            'status' => ['nullable', Rule::in(['open', 'closed', 'temporary'])],
            'misc_paid' => 'nullable|numeric',
            'shipping_cost' => 'nullable|numeric',
            'against_shipping' => 'nullable|numeric',
            'adapter' => 'nullable|numeric',
            'advance' => 'nullable|numeric',
            'discount' => 'nullable|numeric',
            'collection' => 'nullable|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->toJson()
            ], 400);
        }
        $loggedInEmployee = Auth::guard('employee')->user();

        $hasEditManifestPermission = Permission::where([
            ['employee_id', $loggedInEmployee->id],
            ['edit_manifest', 1]
        ])->exists();

        if (!$hasEditManifestPermission) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to edit a manifest'
            ], 403);
        }

        $manifest = Manifest::find($request->manifest_id);

        if (!$manifest) {
            return response()->json([
                'success' => false,
                'message' => 'Manifest not found'
            ], 404);
        }

        $manifest->update($validator->validated());

        if ($request->has('discount') && $request->discount !== null) {
            $manifest->net_total = $manifest->general_total * (1 - ($request->discount / 100));
            $manifest->save();
        }

        return response()->json([
            'success' => true,
            'message' => 'Manifest updated successfully',
            'data' => $manifest
        ], 200);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'An error occurred while updating the manifest',
            'error' => $e->getMessage()
        ], 500);
    }
}

}