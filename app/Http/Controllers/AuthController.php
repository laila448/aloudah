<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Branch_Manager;
use App\Models\Customer;
use App\Models\Driver;
use App\Models\Employee;
use App\Models\User;
use App\Models\Warehouse_Manager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function Register (Request $request)
    {
        try{
        $validator =Validator::make($request->all(),[
            'name'=>'required|min:6|max:255|unique:admins',
            'email'=>'required|string|email|unique:admins',
            'phone_number'=> 'required|max:10',
            'gender'=>'required|in:male,female',
            'password'=>'required|min:8',
            'device_token' => 'required'
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            return response()->json([
                'success' => false,
                'message' => 'Validation failed. Please check the following errors:',
                'errors' => $errors
            ], 400);
        }
    
        $admin=Admin::create(array_merge(
            $validator->validated(),
            ['password'=>bcrypt($request->password)]
        ));
        $credentials=$request->only(['name','password']);
        $token=Auth::guard('admin')->attempt($credentials);

        return response()->json([
            'message'=>'Registered successfully',
            'token'=>$token,
            'admin'=>$admin,
        ],201);
        
    }catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to register.',
            'error' => $e->getMessage()
        ], 500);
    }
    }

    public function Login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255',
            'password' => 'required|min:8',
            'device_token' => 'required'
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            return response()->json([
                'success' => false,
                'message' => 'Validation failed. Please check the following errors:',
                'errors' => $errors
            ], 400);
        }

       $credentials = $request->only(['name', 'password']);

        if ($token = Auth::guard('admin')->attempt($credentials)) {
            $user = Auth::guard('admin')->user();
            $admin = Admin::where('id' , $user->id)->update([
                'device_token' => $request->device_token
            ]);
        return response()->json([
            'token' => $token,
        ]);
        }
       else if ($token = Auth::guard('branch_manager')->attempt($credentials)) {
            $user = Auth::guard('branch_manager')->user();
            $bm = Branch_Manager::where('id' , $user->id)->update([
                'device_token' => $request->device_token
            ]);
        return response()->json([
            'token' => $token,
        ]);
        }
        else if ($token = Auth::guard('employee')->attempt($credentials)) {
            $user = Auth::guard('employee')->user();
            $emp = Employee::where('id' , $user->id)->update([
                'device_token' => $request->device_token
            ]);
        return response()->json([
            'token' => $token,
        ]);
        }
        else if ($token = Auth::guard('customer')->attempt($credentials)) {
            $user = Auth::guard('customer')->user();
            $cust = Customer::where('id' , $user->id)->update([
                'device_token' => $request->device_token
            ]);
        return response()->json([
            'token' => $token,
        ]);
        }
        else if ($token = Auth::guard('warehouse_manager')->attempt($credentials)) {
            $user = Auth::guard('warehouse_manager')->user();
            $wm = Warehouse_Manager::where('id' , $user->id)->update([
                'device_token' => $request->device_token
            ]);
        return response()->json([
            'token' => $token,
        ]);
        }
        else if ($token = Auth::guard('driver')->attempt($credentials)) {
            $user = Auth::guard('driver')->user();
            $driver = Driver::where('id' , $user->id)->update([
                'device_token' => $request->device_token
            ]);
        return response()->json([
            'token' => $token,
        ]);
    }
        return response()->json(['error' => 'Unauthorized'], 401);
    }

    public function Logout(){

        if(Auth::guard('admin')->check()){
            Auth::guard('admin')->logout();
        }
        elseif(Auth::guard('employee')->check()){
            Auth::guard('employee')->logout();
        }
        elseif(Auth::guard('branch_manager')->check()){
            Auth::guard('branch_manager')->logout();
        }
        elseif(Auth::guard('customer')->check()){
            Auth::guard('customer')->logout();
        }
        elseif(Auth::guard('warehouse_manager')->check()){
            Auth::guard('warehouse_manager')->logout();
        }
        elseif(Auth::guard('driver')->check()){
            Auth::guard('driver')->logout();
        }

        return response()->json(['message'=>'Loged out successfully']);

    }
}
