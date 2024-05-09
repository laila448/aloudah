<?php

namespace App\Http\Controllers;

use App\Mail\PasswordMail;
use App\Models\Warehouse;
use App\Models\Warehouse_Manager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class WarehouseController extends Controller
{

    public function addwarehouse(Request $request)
    {

      $validator =  $request->validate([                  
                      'warehouse_address'=>'required',
                      'branch_id'=>'required ',
                      'warehouse_name'=>'required',
                      'area'=>'required ',
                      'notes'=>'required ',
                   
                ]);
              
                $warehouse = new Warehouse();
                $warehouse->address = $validator['warehouse_address']; 
                $warehouse->branch_id = $validator['branch_id'];
                $warehouse->warehouse_name = $validator['warehouse_name'];  
                $warehouse->area = $validator['area']; 
                $warehouse->notes = $validator['notes']; 
                $warehouse->save();

            
               

      return response()->json([
        'message'=>'warehouse added successfully',  
    ],201);

    }
    public function AddWarehouseManager(Request $request)
    {

      $validator =  $request->validate([                  
        'warehouse_id'=>'required',
        'manager_name'=>'required',
        'email'=>'required',
        'phone_number'=>'required ',
        'gender'=>'required',
        'mother_name'=>'required',
        'date_of_birth'=>'required|date_format:Y-m-d',
        'manager_address'=>'required',
       'salary'=>'required',
       'rank'=>'required',
  ]);
             $password = Str::random(8);
                $warehousemanager = new Warehouse_Manager();
                $warehousemanager->warehouse_id = $validator['warehouse_id'];
                $warehousemanager->name = $validator['manager_name'];
                $warehousemanager->email = $validator['email'];
                $warehousemanager->password =  Hash::make($password);
                $warehousemanager->phone_number = $validator['phone_number'];
                $warehousemanager->gender = $validator['gender'];
                $warehousemanager->mother_name = $validator['mother_name'];
                $warehousemanager->date_of_birth = $validator['date_of_birth'];
                 $warehousemanager->manager_address = $validator['manager_address'];
                 $warehousemanager->salary = $validator['salary'];
                 $warehousemanager->rank = $validator['rank'];
                  $warehousemanager->employment_date = now()->format('Y-m-d');
                $warehousemanager->save();
        
                if($warehousemanager){
                  Mail::to($request->email)->send(new PasswordMail($request->manager_name , $password));
                }
                
      return response()->json([
        'message'=>'warehouseManager added successfully',  
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
