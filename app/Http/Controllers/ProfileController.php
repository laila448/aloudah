<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Branch_Manager;
use App\Models\Employee;
use App\Models\Rating;
use App\Models\Vacation;
use App\Models\Warehouse_Manager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function getMyProfile(){

        try{
            if(Auth::guard('admin')->check()){
                $user_id = Auth::guard('admin')->id();
               $admin = Admin::select('rank', 'email' , 'phone_number')
                                ->where('id' , $user_id)->first();
                return response()->json([
                    'success' => true,
                    'data' => $admin ,
                    'message' => 'Admin profile retrieved successfully.'
                ], 200);
            }
            elseif(Auth::guard('employee')->check()){
                $user_id = Auth::guard('employee')->id();
                $employee = Employee::select('rank', 'email' , 'phone_number' , 'address' , 'birth_date')
                                 ->where('id' , $user_id)->first();
                $rating =round(Rating::where('employee_id', $user_id)->avg('rate'),1);
                $vacations = Vacation::where('user_id' , $user_id)
                                    ->where('user_type' , 'employee')->get();
                $employee->rating = $rating;
                $employee->vacations = $vacations;
                 return response()->json([
                     'success' => true,
                     'data' => $employee ,
                     'message' => 'employee profile retrieved successfully.'
                 ], 200);
            }
            elseif(Auth::guard('branch_manager')->check()){
                $user_id = Auth::guard('branch_manager')->id();
                $manager = Branch_Manager::select('rank', 'email' , 'phone_number' , 'manager_address' , 'date_of_birth')
                                 ->where('id' , $user_id)->first();
                 return response()->json([
                     'success' => true,
                     'data' => $manager ,
                     'message' => 'Barnch manager profile retrieved successfully.'
                 ], 200);
            }
            elseif(Auth::guard('warehouse_manager')->check()){
                $user_id = Auth::guard('warehouse_manager')->id();
                $manager = Warehouse_Manager::select('rank', 'email' , 'phone_number' , 'manager_address' , 'date_of_birth')
                                 ->where('id' , $user_id)->first();
                 return response()->json([
                     'success' => true,
                     'data' => $manager ,
                     'message' => 'Warehouse manager profile retrieved successfully.'
                 ], 200);
            }
           

        }catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve your profile.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function editMyProfile(Request $request){

        try{
            if(Auth::guard('admin')->check()){
                $user_id = Auth::guard('admin')->id();
                $admin = Admin::where('id' , $user_id)->update([
                    'phone_number' => $request->phone_number
                ]);
                return response()->json([
                    'success' => true,
                    'message' => 'Admin profile updated successfully.'
                ], 200);
            }
            elseif(Auth::guard('employee')->check()){
                $user_id = Auth::guard('employee')->id();
                $employee = Employee::where('id' , $user_id)->update([
                    'phone_number' => $request->phone_number,
                    'address' => $request->address
                ]);
                 return response()->json([
                     'success' => true,
                     'message' => 'employee profile updated successfully.'
                 ], 200);
            }
            elseif(Auth::guard('branch_manager')->check()){
                $user_id = Auth::guard('branch_manager')->id();
                $manager = Branch_Manager::where('id' , $user_id)->update([
                    'phone_number' => $request->phone_number,
                    'manager_address' => $request->address
                ]);
                 return response()->json([
                     'success' => true,
                     'message' => 'Barnch manager profile updated successfully.'
                 ], 200);
            }
            elseif(Auth::guard('warehouse_manager')->check()){
                $user_id = Auth::guard('warehouse_manager')->id();
                $manager = Warehouse_Manager::where('id' , $user_id)->update([
                    'phone_number' => $request->phone_number,
                    'manager_address' => $request->address
                ]);
                 return response()->json([
                     'success' => true,
                     'message' => 'Warehouse manager profile updated successfully.'
                 ], 200);
            }
           

        }catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update your profile.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
