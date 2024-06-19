<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Driver;
use App\Models\Good;
use App\Models\Manifest;
use App\Models\Price;
use App\Models\Shipping;
use App\Models\Trip;
use App\Models\Truck;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class GoodsController extends Controller
{
    public function AddGood(Request $request){

        $validator = Validator::make($request->all(),[
            'barcode' => 'required|string',
            'quantity' => 'integer'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->toJson()
            ], 400);
        }
        try{
        $shipping = Shipping::where('barcode' , $request->barcode)->first();

        if(!$shipping){
            return response()->json([
                'success' => false,
                'message' => 'Invoice not found'
            ], 404);
        }

        $good = Good::where('barcode' , $request->barcode)->first();

        if($good){
            return response()->json([
                'success' => false,
                'message' => 'This has already been added'
            ], 400);
        }
      //  $manifest = Manifest::where('number' , $shipping->manifest_number)->first();
        $trip = Trip::where('number' , $shipping->manifest_number)->first();
        $price = Price::select('type')->where('id' , $shipping->price_id)->first();
        $truck = Truck::select('line')->where('id' , $trip->truck_id)->first();
        $driver = Driver::select('name')->where('id' , $trip->driver_id)->first();
        $destination = Branch::select('address')->where('id' , $shipping->destination_id)->first();
        $user = Auth::guard('warehouse_manager')->user();
    

        $addGood = Good::create([
            'warehouse_id' => $user->warehouse_id,
            'type' => $price->type,
            'quantity' => $request->input('quantity'),
            'weight' => $shipping->weight ,
            'size' => $shipping->size,
            'content' => $shipping->content,
            'marks' => $shipping->marks,
            'truck' => $truck->line,
            'driver' => $driver->name,
            //'desk' => ,
            'destination' => $destination->address,
            'ship_date' => $shipping->created_at,
            'date' => now()->format('Y-m-d'),
            'sender' => $shipping->sender ,
            'receiver' => $shipping->receiver, 
            'barcode' => $shipping->barcode
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Good has been added successfully'
        ], 200);

    }catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to add this good.',
            'error' => $e->getMessage()
        ], 500);
    }
    }

    public function deleteGood(Request $request){
       
            $validator = Validator::make($request->all(),[
                'barcode' => 'required|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->toJson()
                ], 400);
            }
    try{

        $good = Good::where('barcode' , $request->barcode)->first();

        if(!$good){
            return response()->json([
                'success' => false,
                'message' => 'Good not found'
            ], 404); 
        }

        $good->delete();

        return response()->json([
            'success' => true,
            'message' => 'Good has been deleted'
        ], 200);
    }catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to delete this good.',
            'error' => $e->getMessage()
        ], 500);
    }
    }

    public function receivingGood(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'barcode' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->toJson()
            ], 400);
        }

        try{

            $good = Good::where('barcode' , $request->barcode)->first();

            if(!$good){
                return response()->json([
                    'success' => false,
                    'message' => 'Good not found'
                ], 404); 
            }

            $good->update([
                'received' => true
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Good has been updated successfully'
            ], 200);

        }catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to change status of this good.',
            'error' => $e->getMessage()
        ], 500);
    }
    }

    public function getAllGoods(){

        try{
        $user = Auth::guard('warehouse_manager')->user(); 
        $goods = Good::where('warehouse_id' , $user->warehouse_id)
                       ->where('received' , false)
                       ->paginate(10);
        if(!$goods){
            return response()->json([
                'success' => false,
                'message' => 'No goods found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $goods,
            'message' => 'Goods retrieved successfully.'
        ], 200);
    }catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to retrieved goods.',
            'error' => $e->getMessage()
        ], 500);
    }
    }

    public function getGood(Request $request){
        $validator = Validator::make($request->all(),[
            'barcode' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->toJson()
            ], 400);
        }
        try{
            $good = Good::where('barcode' , $request->barcode)->first();
            if(!$good){
                return response()->json([
                    'success' => false,
                    'message' => 'Good not found'
                ], 404); 
            }

            return response()->json([
                'success' => true,
                'data' => $good ,
                'message' => 'Good retrieved successfully'
            ], 200); 

        }catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieved good.',
                'error' => $e->getMessage()
            ], 500);
        }

    }

    public function inventory(Request $request){

        $validator = Validator::make($request->all() ,[
            'barcodes' => 'required|array',
             'barcodes.*' => 'string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->toJson()
            ], 400);
        }

        try{
            $user = Auth::guard('warehouse_manager')->user(); 
            $existingGoods = Good::where('warehouse_id' , $user->warehouse_id)
                           ->where('received' , false)
                           ->pluck('barcode')
                           ->toArray();
            $notFound = [];
            $found = [];
           
           foreach($existingGoods as $good){
            if(in_array($good,$request->barcodes)){
                   $found[] = $good;
                }
                else{
                  $notFound[] = $good;
                }
            }
            if(empty($notFound)){
                return response()->json([
                    'success' => true,
                    'message' => 'Inventory process completed successfully.',
                ], 200); 
            }

            $notFoundGoods = Good::whereIn('barcode' , $notFound)->get();
            return response()->json([
                'success' => false,
                'message' => 'Some goods are missing .',
                'data' => $notFoundGoods
            ], 400);



        }catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Inventory failed.',
                'error' => $e->getMessage()
            ], 500);
        }
    }


}
