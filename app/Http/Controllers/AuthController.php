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
            'name'=>'required|min:6|max:255',
            'email'=>'required|string|email|unique:users',
            'phone_number'=> 'required|max:10',
            'gender'=>'required|in:male,female',
            'password'=>'required|min:8',
        ]);
        if ($validator->fails())
        {
            return response()->json($validator->errors()->toJson(),400);
        }
        $user=User::create(array_merge(
            $validator->validated(),
            ['password'=>bcrypt($request->password),
            'type' =>'admin'],
        ));
        $admin=Admin::create([
            'user_id'=> $user->id
        ]);
        $credentials=$request->only(['email','password']);
        $token=Auth::guard('admin')->attempt($credentials);

        return response()->json([
            'message'=>'Registered successfully',
            'access_token'=>$token,
            'user'=>$user,
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
        if ( !$token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return response()->json([
            'msg'=>'Login successfully',
            'token'=> $token
        ]);

       // $user = Auth::user();
        //$token = $user->createToken('myapp')->accessToken;

        /*if ($token = Auth::guard('admin')->attempt($credentials)) {
            $user = Auth::guard('admin')->user();
        return response()->json([
            'access_token' => $token,
            'user' => $user
        ]);
        }
       else if ($token = Auth::guard('branch-manager')->attempt($credentials)) {
            $user = Auth::guard('branch-manager')->user();
        return response()->json([
            'access_token' => $token,
            'user' => $user
        ]);
        }*/

        //return response()->json(['error' => 'Unauthorized'], 401);
    }

    public function AddBranchManager (Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'=>'required|min:6|max:255',
            'email'=>'required|string|email|unique:users',
            'phone_number'=> 'required|max:10',
            'gender'=>'required|in:male,female',
            'password'=>'required|min:8',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 422);
        }
        $user = User::create(array_merge(
            $validator->validated(),
            ['password'=>bcrypt($request->password),
            'type' =>'branch-manager'],
        ));
        $branch_manager = Branch_Manager::create([//array_merge(
           // $validator->validated(),
            //['password'=>bcrypt($request->password)]
            //'gender'=> $request->gender,
            'user_id'=>$user->id]
       // )
    );
        return response()->json([
            'message'=>'added successfully',
            'user'=>$user,
        ],201);

    }

    public function test(){
        return response()->json(['msg'=>'it works',
            'user' => auth()->user()
    ]);
    }
}
