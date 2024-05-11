<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Dotenv\Parser\Value;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class CustomerController extends Controller
{
    public function AddCustomer(Request $request){

        $validator = Validator::make($request->all() , [
            'national_id'=>'required|max:11',
            'name'=>'required',
            'phone_number'=>'required|max:10',
            'gender'=>'required|in:male,female',
            'mobile'=>'required|max:10',
            'address'=>'required',
            'address_detail'=>'required',
            'notes'=>'string',
        ]);
        if ($validator->fails())
        {
            return response()->json($validator->errors()->toJson(),400);
        }

        $employee = Auth::guard('employee')->user();
        $password = Str::random(8);
        $customer = Customer::create([
            'national_id'=> $request->national_id,
            'name' => $request->name,
            'password' => Hash::make($password),
            'phone_number' => $request->phone_number,
            'gender' => $request->gender,
            'mobile' => $request->mobile,
            'address' => $request->address,
            'address_detail' => $request->address_detail,
            'notes' => $request->notes,
            'added_by' => $employee->name,
        ]);

        return response()->json(['message' => 'Customer added successfully'], 201);
    }

    public function UpdateCustomer(Request $request){

        $validator = Validator::make($request->all() , [
            'customer_id' => 'required',
            'national_id'=>'max:11',
            'name'=>'string',
            'phone_number'=>'max:10',
            'gender'=>'in:male,female',
            'mobile'=>'max:10',
            'address'=>'string',
            'address_detail'=>'string',
            'notes'=>'string',
        ]);
        if ($validator->fails())
        {
            return response()->json($validator->errors()->toJson(),400);
        }

        $customer = Customer::where('id' , $request->customer_id)->first();
        if($customer){
            $update = $customer->update($validator->validated());
            return response()->json(['message'=>'Customer updated successfully']);
        }

        return response()->json(['message'=>'Customer not found'], 400);
    }

    public function DeleteCustomer(Request $request){

        $validator = Validator::make($request->all(),[
            'customer_id' => 'required'
        ]);
        if ($validator->fails())
        {
            return response()->json($validator->errors()->toJson(),400);
        }

        $customer = Customer::where('id' , $request->customer_id)->first();
        if($customer){
            $delete = $customer->delete();
            return response()->json(['message'=>'Customer deleted successfully']);
        }

        return response()->json(['message'=>'Customer not found'], 400);
    }

    public function GetCustomers(){

        $customers = Customer::all();
        if($customers->isEmpty()){
            return response()->json(['message'=>'No customers found'] , 400);
           
        }
        return response()->json($customers);
    }

    public function GetCustomer(Request $request){

        $validator = Validator::make($request->all(),[
            'customer_id' => 'required'
        ]);
        if ($validator->fails())
        {
            return response()->json($validator->errors()->toJson(),400);
        }

        $customer = Customer::where('id' , $request->customer_id)->first();
        if($customer){
            return response()->json([$customer]);
        }
        return response()->json(['message'=>'Customer not found'], 400);
    }
}
