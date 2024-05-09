<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Branch_Manager;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function Register (Request $request)
    {
        $validator =Validator::make($request->all(),[
            'name'=>'required|min:6|max:255|unique:admins',
            'email'=>'required|string|email|unique:admins',
            'phone_number'=> 'required|max:10',
            'gender'=>'required|in:male,female',
            'password'=>'required|min:8',
        ]);
        if ($validator->fails())
        {
            return response()->json($validator->errors()->toJson(),400);
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
    }

    public function Login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|min:6|max:255',
            'password' => 'required|min:8',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 422);
        }

       $credentials = $request->only(['name', 'password']);

        if ($token = Auth::guard('admin')->attempt($credentials)) {
            $user = Auth::guard('admin')->user();
        return response()->json([
            'token' => $token,
            'user' => $user
        ]);
        }
       else if ($token = Auth::guard('branch_manager')->attempt($credentials)) {
            $user = Auth::guard('branch_manager')->user();
        return response()->json([
            'token' => $token,
            'user' => $user
        ]);
        }
        else if ($token = Auth::guard('employee')->attempt($credentials)) {
            $user = Auth::guard('employee')->user();
        return response()->json([
            'token' => $token,
            'user' => $user
        ]);
        }
        else if ($token = Auth::guard('customer')->attempt($credentials)) {
            $user = Auth::guard('customer')->user();
        return response()->json([
            'token' => $token,
            'user' => $user
        ]);
        }
        else if ($token = Auth::guard('warehouse_manager')->attempt($credentials)) {
            $user = Auth::guard('warehouse_manager')->user();
        return response()->json([
            'token' => $token,
            'user' => $user
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

        return response()->json(['message'=>'Loged out successfully']);

    }
}
