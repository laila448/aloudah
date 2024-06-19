<?php

// namespace App\Http\Controllers;

// use App\Models\Admin;
// use App\Models\Branch_Manager;
// use App\Models\Customer;
// use App\Models\Driver;
// use App\Models\Employee;
// use App\Models\User;
// use App\Models\Warehouse_Manager;
// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Auth;
// use Illuminate\Support\Facades\Validator;

// class AuthController extends Controller
// {
    
//     public function Register (Request $request)
//     {
//         $validator =Validator::make($request->all(),[
//             'name'=>'required|min:6|max:255|unique:admins',
//             'email'=>'required|string|email|unique:admins',
//             'phone_number'=> 'required|max:10',
//             'gender'=>'required|in:male,female',
//             'password'=>'required|min:8',
//             'device_token' => 'required'
//         ]);
//         if ($validator->fails())
//         {
//             return response()->json($validator->errors()->toJson(),400);
//         }
    
//         $admin=Admin::create(array_merge(
//             $validator->validated(),
//             ['password'=>bcrypt($request->password)]
//         ));
//         $credentials=$request->only(['name','password']);
//         $token=Auth::guard('admin')->attempt($credentials);

//         return response()->json([
//             'message'=>'Registered successfully',
//             'token'=>$token,
//             'admin'=>$admin,
//         ],201);
//     }

//     public function Login(Request $request)
//     {
//         $validator = Validator::make($request->all(), [
//             'name' => 'required|min:4|max:255',
//             'password' => 'required|min:8',
//             'device_token' => 'required'
//         ]);
//         if ($validator->fails()) {
//             return response()->json($validator->errors()->toJson(), 422);
//         }

//        $credentials = $request->only(['name', 'password']);

//         if ($token = Auth::guard('admin')->attempt($credentials)) {
//             $user = Auth::guard('admin')->user();
//             $admin = Admin::where('id' , $user->id)->update([
//                 'device_token' => $request->device_token
//             ]);
//         return response()->json([
//             'token' => $token,
//         ]);
//         }
//        else if ($token = Auth::guard('branch_manager')->attempt($credentials)) {
//             $user = Auth::guard('branch_manager')->user();
//             $bm = Branch_Manager::where('id' , $user->id)->update([
//                 'device_token' => $request->device_token
//             ]);
//         return response()->json([
//             'token' => $token,
//         ]);
//         }
//         else if ($token = Auth::guard('employee')->attempt($credentials)) {
//             $user = Auth::guard('employee')->user();
//             $emp = Employee::where('id' , $user->id)->update([
//                 'device_token' => $request->device_token
//             ]);
//         return response()->json([
//             'token' => $token,
//         ]);
//         }
//         else if ($token = Auth::guard('customer')->attempt($credentials)) {
//             $user = Auth::guard('customer')->user();
//             $cust = Customer::where('id' , $user->id)->update([
//                 'device_token' => $request->device_token
//             ]);
//         return response()->json([
//             'token' => $token,
//         ]);
//         }
//         else if ($token = Auth::guard('warehouse_manager')->attempt($credentials)) {
//             $user = Auth::guard('warehouse_manager')->user();
//             $wm = Warehouse_Manager::where('id' , $user->id)->update([
//                 'device_token' => $request->device_token
//             ]);
//         return response()->json([
//             'token' => $token,
//         ]);
//         }
//         else if ($token = Auth::guard('driver')->attempt($credentials)) {
//             $user = Auth::guard('driver')->user();
//             $driver = Driver::where('id' , $user->id)->update([
//                 'device_token' => $request->device_token
//             ]);
//         return response()->json([
//             'token' => $token,
//         ]);
//     }
//         return response()->json(['message' => 'Unauthorized'], 401);
//     }

//     public function Logout(){

//         if(Auth::guard('admin')->check()){
//             Auth::guard('admin')->logout();
//         }
//         elseif(Auth::guard('employee')->check()){
//             Auth::guard('employee')->logout();
//         }
//         elseif(Auth::guard('branch_manager')->check()){
//             Auth::guard('branch_manager')->logout();
//         }
//         elseif(Auth::guard('customer')->check()){
//             Auth::guard('customer')->logout();
//         }
//         elseif(Auth::guard('warehouse_manager')->check()){
//             Auth::guard('warehouse_manager')->logout();
//         }
//         elseif(Auth::guard('driver')->check()){
//             Auth::guard('driver')->logout();
//         }

//         return response()->json(['message'=>'Loged out successfully']);

//     }
// }

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
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Exception;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    private $messaging;

    public function __construct(Factory $firebase)
    {
        $serviceAccountPath = storage_path('app/firebase/firebase_credentials.json');
        $this->messaging = $firebase->withServiceAccount($serviceAccountPath)->createMessaging();
    }

    public function Register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|min:6|max:255|unique:admins',
            'email' => 'required|string|email|unique:admins',
            'phone_number' => 'required|max:10',
            'gender' => 'required|in:male,female',
            'password' => 'required|min:8',
            'device_token' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }

        $admin = Admin::create(array_merge(
            $validator->validated(),
            ['password' => bcrypt($request->password)]
        ));
        $credentials = $request->only(['name', 'password']);
        $token = Auth::guard('admin')->attempt($credentials);

        return response()->json([
            'message' => 'Registered successfully',
            'token' => $token,
            'admin' => $admin,
        ], 201);
    }

    public function Login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|min:4|max:255',
            'password' => 'required|min:8',
            'device_token' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 422);
        }

        $credentials = $request->only(['name', 'password']);
        $user = null;

        if ($token = Auth::guard('admin')->attempt($credentials)) {
            $user = Auth::guard('admin')->user();
            $this->updateDeviceToken($user, Admin::class, $request->device_token);
        } elseif ($token = Auth::guard('branch_manager')->attempt($credentials)) {
            $user = Auth::guard('branch_manager')->user();
            $this->updateDeviceToken($user, Branch_Manager::class, $request->device_token);
        } elseif ($token = Auth::guard('employee')->attempt($credentials)) {
            $user = Auth::guard('employee')->user();
            $this->updateDeviceToken($user, Employee::class, $request->device_token);
        } elseif ($token = Auth::guard('customer')->attempt($credentials)) {
            $user = Auth::guard('customer')->user();
            $this->updateDeviceToken($user, Customer::class, $request->device_token);
        } elseif ($token = Auth::guard('warehouse_manager')->attempt($credentials)) {
            $user = Auth::guard('warehouse_manager')->user();
            $this->updateDeviceToken($user, Warehouse_Manager::class, $request->device_token);
        } elseif ($token = Auth::guard('driver')->attempt($credentials)) {
            $user = Auth::guard('driver')->user();
            $this->updateDeviceToken($user, Driver::class, $request->device_token);
        }

        if ($user) {
            $this->sendLoginNotification($user);
            return response()->json([
                'token' => $token,
            ]);
        }

        return response()->json(['message' => 'Unauthorized'], 401);
    }

    public function Logout()
    {
        if (Auth::guard('admin')->check()) {
            Auth::guard('admin')->logout();
        } elseif (Auth::guard('employee')->check()) {
            Auth::guard('employee')->logout();
        } elseif (Auth::guard('branch_manager')->check()) {
            Auth::guard('branch_manager')->logout();
        } elseif (Auth::guard('customer')->check()) {
            Auth::guard('customer')->logout();
        } elseif (Auth::guard('warehouse_manager')->check()) {
            Auth::guard('warehouse_manager')->logout();
        } elseif (Auth::guard('driver')->check()) {
            Auth::guard('driver')->logout();
        }

        return response()->json(['message' => 'Logged out successfully']);
    }

    private function updateDeviceToken($user, $model, $deviceToken)
    {
        $model::where('id', $user->id)->update([
            'device_token' => $deviceToken
        ]);
    }

    private function sendLoginNotification($user)
    {
        $title = 'Login Successful';
        $body = 'You have successfully logged in.';

        $message = CloudMessage::withTarget('token', $user->device_token)
            ->withNotification(Notification::create($title, $body));

        try {
            $this->messaging->send($message);
            Log::info('Notification sent to user', ['user_id' => $user->id]);
        } catch (Exception $e) {
            Log::error('Failed to send FCM message: ' . $e->getMessage(), ['user_id' => $user->id]);
        }
    }
}

