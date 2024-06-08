<?php

namespace App\Http\Controllers;

use App\Mail\PasswordMail;
use App\Models\Warehouse;
use App\Models\Warehouse_Manager;
use Dotenv\Parser\Value;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class WarehouseController extends Controller
{

    // public function addwarehouse(Request $request)
    // {

    //   $validator =  Validator::make($request->all(), [               
    //                 'warehouse_address'=>'required',
    //                 'branch_id'=>'required ',
    //                 'warehouse_name'=>'required',
    //                 'area'=>'required ',
    //                 'notes'=>'required ',
    //               ]);

    //   if ($validator->fails()){
    //          return response()->json([
    //          'success' => false,
    //          'message' => $validator->errors()->toJson()
    //         ],400);
    //     }
              
           //     $warehouse = new Warehouse();
           //     $warehouse->address = $request->warehouse_address; 
            //    $warehouse->branch_id = $request->branch_id;
            //    $warehouse->warehouse_name = $request->warehouse_name;  
            //    $warehouse->area = $request->area; 
            //    $warehouse->notes = $request->notes; 
            //    $warehouse->save();

            
               

    //   return response()->json([
    //     'success' => true ,
    //     'message'=>'warehouse added successfully',  
    // ],201);

    // }
    public function addWarehouse(Request $request)
{
    try {
        // Validate the request data
        $validator = Validator::make($request->all(), [               
            'warehouse_address' => 'required|string',
            'branch_id' => 'required|numeric',
            'warehouse_name' => 'required|string',
            'area' => 'required|string',
            'notes' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->toJson()
            ], 400);
        }
        $warehouse = new Warehouse();
        $warehouse->address = $request->input('warehouse_address'); 
        $warehouse->branch_id = $request->input('branch_id');
        $warehouse->warehouse_name = $request->input('warehouse_name');  
        $warehouse->area = $request->input('area'); 
        $warehouse->notes = $request->input('notes'); 
        $warehouse->save();

        return response()->json([
            'success' => true,
            'message' => 'Warehouse added successfully'
        ], 200);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'An error occurred while adding the warehouse',
            'error' => $e->getMessage()
        ], 500);
    }
}

  //   public function AddWarehouseManager(Request $request)
  //   {

  //     $validator = Validator::make($request->all(),[                  
  //       'warehouse_id'=>'required',
  //       'national_id'=>'required|max:11',
  //       'manager_name'=>'required',
  //       'email'=>'required',
  //       'phone_number'=>'required ',
  //       'gender'=>'required',
  //       'mother_name'=>'required',
  //       'date_of_birth'=>'required|date_format:Y-m-d',
  //       'manager_address'=>'required',
  //      'salary'=>'required',
  //      'rank'=> ['required',Rule::in(['warehouse_manager'])  ],

  // ]);

  // if ($validator->fails()){
  //           return response()->json([
  //           'success' => false,
  //           'message' => $validator->errors()->toJson()
  //           ],400);
  //       }
  //               $password = Str::random(8);
  //               $warehousemanager = new Warehouse_Manager();
  //               $warehousemanager->warehouse_id = $validator['warehouse_id'];
  //               $warehousemanager->national_id = $validator['national_id'];
  //               $warehousemanager->name = $validator['manager_name'];
  //               $warehousemanager->email = $validator['email'];
  //               $warehousemanager->password =  Hash::make($password);
  //               $warehousemanager->phone_number = $validator['phone_number'];
  //               $warehousemanager->gender = $validator['gender'];
  //               $warehousemanager->mother_name = $validator['mother_name'];
  //               $warehousemanager->date_of_birth = $validator['date_of_birth'];
  //               $warehousemanager->manager_address = $validator['manager_address'];
  //               $warehousemanager->salary = $validator['salary'];
  //               $warehousemanager->rank = $validator['rank'];
  //               $warehousemanager->employment_date = now()->format('Y-m-d');
  //               $warehousemanager->save();
        
  //               if($warehousemanager){
  //                 Mail::to($request->email)->send(new PasswordMail($request->manager_name , $password));
  //               }
                
  //     return response()->json([
  //       'success' => true ,
  //       'message'=>'warehouseManager added successfully',  
  //   ],201);



  //   }
  public function AddWarehouseManager(Request $request)
  {
      try {
          $validator = Validator::make($request->all(), [                  
              'warehouse_id' => 'required|numeric',
              'national_id' => 'required|max:11|unique:warehouse_managers,national_id',
              'manager_name' => 'required|string',
              'email' => 'required|email|unique:warehouse_managers,email',
              'phone_number' => 'required|unique:warehouse_managers,phone_number',
              'gender' => 'required|in:male,female',
              'mother_name' => 'required|string',
              'date_of_birth' => 'required|date_format:Y-m-d',
              'manager_address' => 'required|string',
              'salary' => 'required|numeric',
              'rank' => ['required', Rule::in(['warehouse_manager'])],
          ]);
  
          if ($validator->fails()) {
              return response()->json([
                  'success' => false,
                  'message' => $validator->errors()->toJson()
              ], 400);
          }
          $password = Str::random(8);
          $warehouseManager = new Warehouse_Manager();
          $warehouseManager->warehouse_id = $request->input('warehouse_id');
          $warehouseManager->national_id = $request->input('national_id');
          $warehouseManager->name = $request->input('manager_name');
          $warehouseManager->email = $request->input('email');
          $warehouseManager->password = Hash::make($password);
          $warehouseManager->phone_number = $request->input('phone_number');
          $warehouseManager->gender = $request->input('gender');
          $warehouseManager->mother_name = $request->input('mother_name');
          $warehouseManager->date_of_birth = $request->input('date_of_birth');
          $warehouseManager->manager_address = $request->input('manager_address');
          $warehouseManager->salary = $request->input('salary');
          $warehouseManager->rank = $request->input('rank');
          $warehouseManager->employment_date = now()->format('Y-m-d');
          $warehouseManager->save();
  
          if ($warehouseManager) {
              Mail::to($warehouseManager->email)->send(new PasswordMail($warehouseManager->name, $password));
          }
  
          return response()->json([
              'success' => true,
              'message' => 'Warehouse Manager added successfully'
          ], 200);
  
      } catch (\Exception $e) {
          return response()->json([
              'success' => false,
              'message' => 'An error occurred while adding the warehouse manager',
              'error' => $e->getMessage()
          ], 500);
      }
  }
  
    public function UpdateWarehouse(Request $request)
    {
      $validator =Validator::make($request->all(),[
        'address'=>'string',
        'branch'=>'string',
        'notes'=>'string',
        'area'=>'string',
        'phone'=>'numeric|min:4',
        'national_id'=>'max:11',
        'name'=>'min:5|max:255',
        'phone_number'=> 'max:10',
        'manager_address'=>'string',
        'gender'=>'in:male,female',
        'mother_name'=>'string',
        'birth_date'=>'date_format:Y-m-d',
        'salary'=>'string',
        'rank'=>'string',
        
    ]);
    if ($validator->fails()){
      return response()->json([
      'success' => false,
      'message' => $validator->errors()->toJson()
      ],400);
      }

      $warehouse = Warehouse::find($request->warehouse_id);
      $updatedbranch= $warehouse->update($request->all());


      $w_Manager = Warehouse_Manager::where('warehouse_id', $request->warehouse_id)->first();
      $updatedmanager= $w_Manager->update($request->all());

      return response()->json([
        'success' => true ,
        'message' => 'warehouse updated successfully' 
      ], 200);
    }

    // public function deleteWarehouse(Request $request)
    // {
    //   $validator =Validator::make($request->all(),[
    //     'warehouse_id'=>'required|numeric',
    // ]);

    // if ($validator->fails()){
    //   return response()->json([
    //   'success' => false,
    //   'message' => $validator->errors()->toJson()
    //   ],400);
    //   }

    //   $Warehouse = Warehouse::find($request->warehouse_id)->delete();
    //   $WarehouseManager = Warehouse_Manager::where('warehouse_id', $request->warehouse_id)->delete();

    //     return response()->json([
    //       'success' => true ,
    //       'msg'=>'Warehouse has been deleted'], 200) ;
    // }
    public function deleteWarehouse(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'warehouse_id' => 'required|numeric',
            ]);
    
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->toJson()
                ], 400);
            }
                $warehouse = Warehouse::find($request->warehouse_id);
    
            if (!$warehouse) {
                return response()->json([
                    'success' => false,
                    'message' => 'Warehouse not found'
                ], 404);
            }
                $warehouse->delete();
                Warehouse_Manager::where('warehouse_id', $request->warehouse_id)->delete();
    
            return response()->json([
                'success' => true,
                'message' => 'Warehouse has been deleted'
            ], 200);
    
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while deleting the warehouse',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    // public function GetWarehouses(){

    //   $warehouses  = Warehouse::paginate(10);
    //   if($warehouses){
    //     return response()->json([
    //       'success' => true ,
    //       'data' => $warehouses
    //     ] , 200); 
    //   }

    //   return response()->json([
    //     'success' => false ,
    //     'message'=>'No warehouses found'], 404);
    // }
    public function GetWarehouses()
    {
        try {
            $warehouses = Warehouse::paginate(10);    
            if ($warehouses->isNotEmpty()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Warehouses retrieved successfully',
                    'data' => $warehouses
                ], 200);
            }
    
            return response()->json([
                'success' => false,
                'message' => 'No warehouses found'
            ], 404);
    
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while retrieving the warehouses',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    // public function GetWarehouseManager( $id){


    // $whmanager = Warehouse_Manager::where('warehouse_id' , $id)->first();
    // if($whmanager){
    //   return response()->json([
    //     'success' => true ,
    //     'data' => $whmanager
    //   ] , 200); 
    // }
    // return response()->json([
    //   'success' => false ,
    //   'message'=>'Warehouse manager not found'], 404);
    // }
    public function GetWarehouseManager($id)
    {
        try {
            $whmanager = Warehouse_Manager::where('warehouse_id', $id)->first();    
            if ($whmanager) {
                return response()->json([
                    'success' => true,
                    'message' => 'Warehouse manager retrieved successfully',
                    'data' => $whmanager
                ], 200);
            }
    
            return response()->json([
                'success' => false,
                'message' => 'Warehouse manager not found'
            ], 404);
    
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while retrieving the warehouse manager',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
}
