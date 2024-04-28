<?php

namespace App\Http\Controllers;

use App\Models\Warehouse;
use App\Models\Warehouse_Manager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class WarehouseController extends Controller
{

    public function addwarehouse(Request $request)
    {

      $validator =  $request->validate([                  
                      'warehouse_address'=>'required',
                      'branch'=>'required ',
                      'area'=>'required ',
                      'notes'=>'required ',
                      'manager_name'=>'required',
                      'email'=>'required',
                      'password'=>'required',
                      'phone_number'=>'required ',
                      'gender'=>'required',
                      'mother_name'=>'required',
                      'date_of_birth'=>'required|date_format:Y-m-d',
                      'manager_address'=>'required',
                     'salary'=>'required',
                     'rank'=>'required',
                ]);
              
                $warehouse = new Warehouse();
                $warehouse->branch = $validator['branch']; 
                $warehouse->address = $validator['warehouse_address']; 
                $warehouse->area = $validator['area']; 
                $warehouse->notes = $validator['notes']; 
                $warehouse->save();

                $warehousemanager = new Warehouse_Manager();
                $warehousemanager->name = $validator['manager_name'];
                $warehousemanager->email = $validator['email'];
                $warehousemanager->password =  Hash::make($validator['password']);
                $warehousemanager->phone_number = $validator['phone_number'];
               $warehousemanager->warehouse_id = $warehouse->id;
                $warehousemanager->gender = $validator['gender'];
                $warehousemanager->mother_name = $validator['mother_name'];
                $warehousemanager->date_of_birth = $validator['date_of_birth'];
                 $warehousemanager->manager_address = $validator['manager_address'];
                 $warehousemanager->salary = $validator['salary'];
                 $warehousemanager->rank = $validator['rank'];
                  $warehousemanager->employment_date = now()->format('Y-m-d');
                $warehousemanager->save();
        
               

      return response()->json([
        'message'=>'warehouse added successfully',  
    ],201);

    }


    public function UpdateWarehouse(Request $request)
    {
      $validator =Validator::make($request->all(),[
        'address'=>'string',
        'branch'=>'string',
        'notes'=>'string',
        'area'=>'string',
        'phone'=>'numeric|min:4',
        'name'=>'min:5|max:255',
        'phone_number'=> 'max:10',
        'manager_address'=>'string',
        'gender'=>'in:male,female',
        'mother_name'=>'string',
        'birth_date'=>'date_format:Y-m-d',
        'salary'=>'string',
        'rank'=>'string',
        
    ]);
    if ($validator->fails())
    {
        return response()->json($validator->errors()->toJson(),400);
    }

      $warehouse = Warehouse::find($request->warehouse_id);
      $updatedbranch= $warehouse->update($request->all());


      $w_Manager = Warehouse_Manager::where('warehouse_id', $request->warehouse_id)->first();
      $updatedmanager= $w_Manager->update($request->all());

      return response()->json(['message' => 'warehouse updated successfully']);
    }

    public function deleteWarehouse(Request $request)
    {
      $validator =Validator::make($request->all(),[
        'warehouse_id'=>'required|numeric',
    ]);

    if ($validator->fails())
    {
        return response()->json($validator->errors()->toJson(),400);
    }

        $Warehouse = Warehouse::find($request->warehouse_id)->delete();
      $WarehouseManager = Warehouse_Manager::where('warehouse_id', $request->warehouse_id)->delete();

        return response()->json(['msg'=>'Warehouse has been deleted'], 200) ;
    }
   
}
