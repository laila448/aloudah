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
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification as FCMNotification;
use Exception;
use Illuminate\Support\Facades\Log;
use App\Models\Notification;

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

        $admin = Admin::create(array_merge(
            $validator->validated(),
            ['password' => bcrypt($request->password)]
        ));
        $credentials = $request->only(['name', 'password']);
        $token = Auth::guard('admin')->attempt($credentials);

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
            ->withNotification(FCMNotification::create($title, $body));

        try {
            $this->messaging->send($message);
            Log::info('Notification sent to user', ['user_id' => $user->id]);
        } catch (Exception $e) {
            Log::error('Failed to send FCM message: ' . $e->getMessage(), ['user_id' => $user->id]);
        }
    }

     public function getNotifications(Request $request)
    {
        $user = Auth::guard('admin')->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        $notifications = Notification::where('admin_id', $user->id)->get();

        return response()->json([
            'success' => true,
            'data' => $notifications
        ], 200);
    }
    public function getRole(Request $request)
    {
        $user = null;

        if (Auth::guard('admin')->check()) {
            $user = Auth::guard('admin')->user();
            $role = 'admin';
        } elseif (Auth::guard('branch_manager')->check()) {
            $user = Auth::guard('branch_manager')->user();
            $role = 'branch_manager';
        } elseif (Auth::guard('employee')->check()) {
            $user = Auth::guard('employee')->user();
            $role = 'employee';
        } elseif (Auth::guard('customer')->check()) {
            $user = Auth::guard('customer')->user();
            $role = 'customer';
        } elseif (Auth::guard('warehouse_manager')->check()) {
            $user = Auth::guard('warehouse_manager')->user();
            $role = 'warehouse_manager';
        } elseif (Auth::guard('driver')->check()) {
            $user = Auth::guard('driver')->user();
            $role = 'driver';
        }

        if ($user) {
            return response()->json([
                'success' => true,
                'role' => $role,
              //  'user' => $user,
            ], 200);
        }

        return response()->json([
            'success' => false,
            'message' => 'Unauthorized'
        ], 401);
    }
}

