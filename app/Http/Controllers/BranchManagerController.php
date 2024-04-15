<?php

namespace App\Http\Controllers;

use App\Models\Driver;
use App\Models\Employee;
use App\Models\Truck;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class BranchManagerController extends Controller
{
    public function AddEmployee(Request $request){

        $validator =Validator::make($request->all(),[
            'name'=>'required|min:5|max:255|unique:employees',
            'email'=>'string|email|unique:employees',
            'phone_number'=> 'required|max:10',
            'gender'=>'required|in:male,female',
            'password'=>'min:8',
            'branch_id'=>'required',
            'mother_name'=>'required|string',
            'birth_date'=>'required',
            'birth_place'=>'required|string',
            'mobile'=>'required',
            'address'=>'required|string',
            'salary'=>'required',
            'rank'=>'required',
        ]);
        if ($validator->fails())
        {
            return response()->json($validator->errors()->toJson(),400);
        }

        $manager = Auth::guard('branch_manager')->user();
        if($request->rank == 'driver')
        {
            $driver=Driver::create([
                'name'=>$request->name,
                'phone_number'=> $request->phone_number,
                'gender'=>$request->gender,
                'branch_id'=>$request->branch_id,
                'mother_name'=>$request->mother_name,
                'birth_date'=>$request->birth_date,
                'birth_place'=>$request->birth_place,
                'mobile'=>$request->mobile,
                'address'=>$request->address,
                'salary'=>$request->salary,
                'rank'=>$request->rank,
                'employment_date'=>now()->format('Y-m-d'),
                'manager_name'=>$manager->name,
           ]);
           return response()->json([
            'message'=>'Driver addedd successfully',
        ],201);
        }
            else{
        $employee=Employee::create(array_merge(
            $validator->validated(),
            ['password'=>bcrypt($request->password),
             'employment_date'=>now()->format('Y-m-d'),
             'manager_name'=>$manager->name
            ]
        ));
        return response()->json([
            'message'=>'Employee addedd successfully',
        ],201);
    }
       
    }

    public function UpdateEmployee(Request $request){

        $validator =Validator::make($request->all(),[
            'name'=>'min:5|max:255|unique:employees',
            'email'=>'string|email|unique:employees',
            'phone_number'=> 'max:10',
            'gender'=>'in:male,female',
            'password'=>'min:8',
            'branch_id'=>'numeric',
            'mother_name'=>'string',
            'birth_date'=>'date_format:Y-m-d',
            'birth_place'=>'string',
            'mobile'=>'max:10',
            'address'=>'string',
            'employee_id'=>'required|numeric'
        ]);
        if ($validator->fails())
        {
            return response()->json($validator->errors()->toJson(),400);
        }

        $employee = Employee::where('id' , $request->employee_id)->first();

        if($employee){

            $updated_employee = $employee->update( array_merge(
                $validator->validated(),
                ['password'=>bcrypt($request->password)]
            ));

            return response()->json([
                'message'=>'Employee updated successfully',
            ],200); 
        }
    }

    public function UpdateDriver(Request $request){

        $validator =Validator::make($request->all(),[
            'name'=>'min:5|max:255|unique:drivers',
            'phone_number'=> 'max:10',
            'gender'=>'in:male,female',
            'branch_id'=>'numeric',
            'mother_name'=>'string',
            'birth_date'=>'date_format:Y-m-d',
            'birth_place'=>'string',
            'mobile'=>'max:10',
            'address'=>'string', 
            'driver_id'=>'required|numeric'
        ]);
        if ($validator->fails())
        {
            return response()->json($validator->errors()->toJson(),400);
        }

        $driver = Driver::where('id' , $request->driver_id)->first();

        if($driver){

            $updated_driver = $driver->update( $validator->validated());

            return response()->json([
                'message'=>'Driver updated successfully',
            ],200); 
        }
    }

    public function DeleteEmployee(Request $request){

        $validator =Validator::make($request->all(),[
            'employee_id'=>'required|numeric',
        ]);

        if ($validator->fails())
        {
            return response()->json($validator->errors()->toJson(),400);
        }

        $employee = Employee::where('id' , $request->employee_id)->first();

        if($employee){
           $deletedemp = $employee->delete();

           return response()->json(['message'=>'Employee has been deleted'], 200,); 
        }
    }

    public function DeleteDriver(Request $request){

        $validator =Validator::make($request->all(),[
            'driver_id'=>'required|numeric',
        ]);

        if ($validator->fails())
        {
            return response()->json($validator->errors()->toJson(),400);
        }

        $driver = Driver::where('id' , $request->driver_id)->first();

        if($driver){
           $deleteddriver = $driver->delete();

           return response()->json(['message'=>'Driver has been deleted'], 200,); 
        }
    }



    public function AddTruck(Request $request){

        $validator =Validator::make($request->all(),[
            'number'=>'required|min:4|max:20',
            'line'=>'string|required',
            'notes'=>'string',
        ]);

        if ($validator->fails())
        {
            return response()->json($validator->errors()->toJson(),400);
        }
        $createdby = Auth::guard('branch_manager')->user()->name;
        $truck=Truck::create([
            'number'=>$request->number,
            'line'=> $request->line,
            'notes'=>$request->notes,
            'created_by'=>$createdby,
            'adding_data'=>now()->format('Y-m-d'),
            
        ]);
      
        return response()->json([
            'message'=>'Truck addedd successfully',
        ],201);
    
       
    }



    public function UpdateTruck(Request $request){

        $validator =Validator::make($request->all(),[
            'truck_id'=>'required',
            'number'=>'min:4|max:20',
            'line'=>'string',
            'notes'=>'string',
        ]);

        if ($validator->fails())
        {
            return response()->json($validator->errors()->toJson(),400);
        }
      
      
        $edit_by = Auth::guard('branch_manager')->user()->name;
         
        $truck = Truck::where('id' , $request->truck_id)->first();

        if($truck){

            $updated_truck = $truck->update( array_merge(
                $validator->validated(),
                ['editing_by'=>$edit_by,
                'editing_date'=>now()->format('Y-m-d'),
                ]
            ));

        }
      
        return response()->json([
            'message'=>'Truck updated successfully',
        ],201);
    
       
    }

    public function DeleteTruck(Request $request){

        $validator =Validator::make($request->all(),[
            'truck_id'=>'required',
        ]);

        if ($validator->fails())
        {
            return response()->json($validator->errors()->toJson(),400);
        }


        $truck = Truck::find($request->truck_id)->delete();
      
        return response()->json([
            'message'=>'Truck deleted successfully',
        ],201);
        }
 
}
