<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Branch_Manager;
use App\Models\Customer;
use App\Models\Driver;
use App\Models\Employee;
use App\Models\Warehouse_Manager;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;

class ResetPasswordController extends Controller
{
    public function showResetForm(Request $request, $token = null)
    {
        return view('mail.resetpassword')->with(
            ['token' => $token, 'email' => $request->email , 'guard' => $request->guard]
        );
    }

    public function SendResetLink(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'email' => 'required|email',
            'guard' => 'required|string|in:admin,branch_manager,driver,employee,customer,warehouse_manager',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->toJson()
            ], 400);
        }
        try{
        $user = null;
        if($request->guard == 'warehouse_manager'){
        $user = Warehouse_Manager::where('email', $request->email)->first();
        }elseif($request->guard == 'admin'){
            $user = Admin::where('email', $request->email)->first();
        }elseif($request->guard == 'employee'){
            $user = Employee::where('email', $request->email)->first();
        }elseif($request->guard == 'driver'){
            $user = Driver::where('email', $request->email)->first();
        }elseif($request->guard == 'branch_manager'){
            $user = Branch_Manager::where('email', $request->email)->first();
        }elseif($request->guard == 'customer'){
            $user = Customer::where('email', $request->email)->first();
        }

        $token = Password::createToken($user);
        Notification::send($user, new ResetPasswordNotification($token , $request->guard));

            return response()->json([
                'success' => true,
                'message' => 'Reset link sent successfully'
            ],200);

        }catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send reset link.',
                'error' => $e->getMessage()
            ], 500);
        }
       
    }

    public function ResetPassword(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|string|min:8|confirmed',
            'guard' => 'required|string|in:admin,branch_manager,driver,employee,customer,warehouse_manager',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->toJson()
            ], 400);
        }
        $broker = $request->guard.'s';
        $status = Password::broker($broker)->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => bcrypt($password),
                ])->save();
            }
        );

        if($status == Password::PASSWORD_RESET){
           return response()->json([
                'success' => true,
                'message' => 'Reset password successfully'
            ],200);
        }

        return response()->json([
            'success' => false,
            'message' => 'Somthing went wrong'
        ],500);
    }


 //   protected function sendResetResponse($response)
 //   {
 //       return redirect()->route('password.reset.result')->with('status', trans($response));
 //   }
 //
 //   protected function sendResetFailedResponse($response)
 //   {
 //       return redirect()->route('password.reset.result')->withErrors(['email' => trans($response)]);
 //   }
}
