<?php

namespace App\Http\Controllers;

use App\Models\Price;
use App\Models\Shipping;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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




}
