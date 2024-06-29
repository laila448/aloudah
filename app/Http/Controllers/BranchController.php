<?php

namespace App\Http\Controllers;

use App\Mail\PasswordMail;
use App\Models\Branch;
use App\Models\Branch_Manager;
use App\Models\Employee;
use Dotenv\Parser\Value;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Database\QueryException;
use App\Models\Trip;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification as FCMNotification;
use Illuminate\Support\Facades\Log;
use Exception;
use App\Models\Notification;
use App\Models\Employee;

class BranchController extends Controller
{
    private $messaging;

    public function __construct(Factory $firebase)
    {
        $serviceAccountPath = storage_path('app/firebase/firebase_credentials.json');
        $this->messaging = $firebase->withServiceAccount($serviceAccountPath)->createMessaging();
    }

  public function GetAllBranches()
  {
      try {
          $branches = Branch::select('id', 'address', 'desk')->paginate(5);
          return response()->json([
              'success' => true,
              'data' => $branches,
              'message' => 'Branches retrieved successfully.'
          ], 200);
  
      } catch (\Exception $e) {
          return response()->json([
              'success' => false,
              'message' => 'Failed to retrieve branches.',
              'error' => $e->getMessage()
          ], 500);
      }
  }


     public function getBranchlatlng( $id)
     {
      
      $branch = Branch::select('branch_lat', 'branch_lng')->where('id', $id)->first();

      if (!$branch) {
          return response()->json([
            'success' => false ,
            'message' => 'Branch not found'
          ], 404);
      }
      
       return response()->json([
        'success' => true ,
        'data' => $branch ,
        'message' => 'Branch location retrieved successfully.'
        ] , 200) ;
  
       
    }

     }
    //public function AddBranch (Request $request)
    //{
    //    $validator =  Validator::make($request->all(),[
    //     'desk'=>'required|min:3',
    //      'address'=>'required',
    //       'phone'=>'required|min:4|max:15',
          
    //     ]);
      
    //     if ($validator->fails())
    //     {
    //         return response()->json([
    //          'success' => false,
    //          'message' => $validator->errors()->toJson()
    //         ],400);
    //     }

    //     $branch = new Branch();
    //   $branch->desk = $validator['desk'];
    //     $branch->address = $validator['address'];
    //     $branch->phone = $validator['phone'];
    //     $branch->opening_date = now()->format('Y-m-d');
    //     $branch->created_by = Auth::guard('admin')->user()->name;
    //     $branch->save();
      

    //             return response()->json([
    //               'success' => true ,
    //               'message'=>'branch added successfully',  
    //             ],200);
   
    // }
//     public function AddBranch(Request $request)
// {
//     $validator = Validator::make($request->all(), [
//         'desk' => 'required|min:3',
//         'address' => 'required',
//         'phone' => 'required|min:4|max:15',
//     ]);

//     if ($validator->fails()) {
//         return response()->json([
//             'success' => false,
//             'message' => $validator->errors()->toJson()
//         ], 400);
//     }

//     $branch = new Branch();
//     $branch->desk = $request->input('desk');
//     $branch->address = $request->input('address');
//     $branch->phone = $request->input('phone');
//     $branch->opening_date = now()->format('Y-m-d');
//     $branch->created_by = Auth::guard('admin')->user()->name;

//     $branch->save();

//     return response()->json([
//         'success' => true,
//         'message' => 'Branch added successfully'
//     ], 200);
// }
public function AddBranch(Request $request)
{
    try {
        $validator = Validator::make($request->all(), [
            'desk' => 'required|min:3',
            'address' => 'required|string',
            'phone' => 'required|min:4|max:15',
            'branch_lat' => 'required',
            'branch_lng' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->toJson()
            ], 400);
        }

        $branch = new Branch();
      $branch->desk = $request->desk;
        $branch->address = $request->address;
        $branch->phone = $request->phone;
        $branch->branch_lat = $request->branch_lat;
        $branch->branch_lng = $request->branch_lng;
        $branch->opening_date = now()->format('Y-m-d');
        $branch->created_by = Auth::guard('admin')->user()->name;

        $branch->save();

        return response()->json([
            'success' => true,
            'message' => 'Branch added successfully'
        ], 200);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'An error occurred while adding the branch',
            'error' => $e->getMessage()
        ], 500);
    }
}

    // public function AddBranchManager (Request $request)
    // {
    //     $validator = Validator::make($request->all() ,[
    //         'branch_id'=>'required',
    //         'national_id'=>'required|max:11',
    //          'manager_name'=>'required',
    //          'email'=>'required',
    //          'phone_number'=>'required ',
    //          'gender'=>'required',   
    //          'mother_name'=>'required',
    //          'date_of_birth'=>'required|date_format:Y-m-d',
    //          'manager_address'=>'required',
    //         'salary'=>'required',
    //         'rank'=> ['required',Rule::in(['Branch_manager'])  ],
          
    //     ]);
      
    //     if ($validator->fails())
    //     {
    //         return response()->json([
    //          'success' => false,
    //          'message' => $validator->errors()->toJson()
    //         ],400);
    //     }

    //    $password = Str::random(8);
    //    $branchmanager = new Branch_Manager();
    //            $branchmanager->national_id = $request->national_id;
    //            $branchmanager->name = $request->manager_name;
    //            $branchmanager->email = $request->email;
    //            $branchmanager->password = Hash::make($password); 
    //            $branchmanager->phone_number = $request->phone_number;
    //            $branchmanager->branch_id = $request->branch_id;
    //            $branchmanager->gender = $request->gender;
    //            $branchmanager->mother_name = $request->mother_name; 
    //            $branchmanager->date_of_birth = $request->date_of_birth;
    //            $branchmanager->manager_address = $request->manager_address;
    //            $branchmanager->salary = $request->salary;
    //           $branchmanager->rank = $request->rank;
    //            $branchmanager->employment_date = now()->format('Y-m-d');
    //            $branchmanager->save();

    //             if($branchmanager){
    //               Mail::to($request->email)->send(new PasswordMail($request->manager_name , $password));
    //             }
    //             return response()->json([
    //               'success' => true ,
    //               'message'=>'branch manager added successfully',  
    //             ],200);
   
    // }

public function AddBranchManager(Request $request)
{
    $validator = Validator::make($request->all(), [
        'branch_id' => 'required',
        'national_id' => 'required|max:11|unique:branch_managers,national_id',
        'manager_name' => 'required|unique:branch_managers,name',
        'email' => 'required|email|unique:branch_managers,email',
        'phone_number' => 'required|min:4|max:15|unique:branch_managers,phone_number',
        'gender' => 'required',
        'mother_name' => 'required',
        'date_of_birth' => 'required|date_format:Y-m-d',
        'manager_address' => 'required',
        'salary' => 'required',
        'rank' => ['required', Rule::in(['Branch_manager'])],
    ]);

    if ($validator->fails()) {
        $errors = $validator->errors()->all();
        return response()->json([
            'success' => false,
            'message' => 'Validation failed. Please check the following errors:',
            'errors' => $errors
        ], 400);
    }

    try {
        $password = Str::random(8);
        $branchManager = new Branch_Manager();
        $branchManager->national_id = $request->input('national_id');
        $branchManager->name = $request->input('manager_name');
        $branchManager->email = $request->input('email');
        $branchManager->password = Hash::make($password);
        $branchManager->phone_number = $request->input('phone_number');
        $branchManager->branch_id = $request->input('branch_id');
        $branchManager->gender = $request->input('gender');
        $branchManager->mother_name = $request->input('mother_name');
        $branchManager->date_of_birth = $request->input('date_of_birth');
        $branchManager->manager_address = $request->input('manager_address');
        $branchManager->salary = $request->input('salary');
        $branchManager->rank = $request->input('rank');
        $branchManager->employment_date = now()->format('Y-m-d');
        $branchManager->save();

        $branch = Branch::find($request->branch_id);
        if ($branch) {
            $branch->branchmanager_id = $branchManager->id;
            $branch->save();
        }

        Mail::to($branchManager->email)->send(new PasswordMail($branchManager->name, $password));

        $notificationStatus = $this->sendBranchManagerAddedNotification($branchManager);

        return response()->json([
            'success' => true,
            'message' => 'Branch manager added successfully',
            'notification_status' => $notificationStatus
        ], 200);
    } catch (QueryException $e) {
        $errorCode = $e->errorInfo[1];
        if ($errorCode == 1062) {
            return response()->json([
                'success' => false,
                'message' => 'A manager with the same National ID, Email, Phone Number, or Manager Name already exists. Please ensure all fields are unique.'
            ], 400);
        }

        return response()->json([
            'success' => false,
            'message' => 'Failed to add branch manager.',
            'error' => $e->getMessage()
        ], 500);
    }
}

    private function sendBranchManagerAddedNotification($branchManager)
    {
        $title = 'New Branch Manager Added';
        $body = "Branch Manager '{$branchManager->name}' has been successfully added.";
    
        $admin = Auth::guard('admin')->user();
        $deviceToken = $admin->device_token;
    
        if ($deviceToken) {
            $message = CloudMessage::withTarget('token', $deviceToken)
                ->withNotification(FCMNotification::create($title, $body));
    
            try {
                $this->messaging->send($message);
                Log::info('Notification sent: Branch Manager Added', ['branch_manager_id' => $branchManager->id, 'admin' => $admin->name]);

                Notification::create([
                    'admin_id' => $admin->id,
                    'title' => $title,
                    'body' => $body,
                    'status' => 'sent',
                    'created_at' => now()
                ]);
                return 'Notification sent successfully';
            } catch (Exception $e) {
                Log::error('Failed to send FCM message: ' . $e->getMessage(), ['branch_manager_id' => $branchManager->id, 'admin' => $admin->name]);
                return 'Failed to send notification';
            }
        } else {
            Log::warning('Admin device token not found, notification not sent.', ['admin' => $admin->name]);
            return 'Admin device token not found';
        }
    }
    
    public function UpdateBranch(Request $request)
    {
        try {
            $user = Auth::guard('admin')->user();
            $validator = Validator::make($request->all(), [
                'branch_id' => 'required|numeric',
                'address' => 'string',
                'phone' => 'numeric|min:4',
                'name' => 'min:5|max:255',
                'phone_number' => 'max:10',
                'manager_address' => 'string',
                'gender' => 'in:male,female',
                'mother_name' => 'string',
                'birth_date' => 'date_format:Y-m-d',
                'salary' => 'string',
                'rank' => 'string',
            ]);
    
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->toJson()
                ], 400);
            }
    
            $branch = Branch::find($request->branch_id);
    
            if (!$branch) {
                return response()->json([
                    'success' => false,
                    'message' => 'Branch not found'
                ], 404);
            }
    
            $branch->update(array_merge($validator->validated(), [
                'edited_by' => $user->name,
                'editing_date' => now()->format('Y-m-d')
            ]));
    
            $branchManager = Branch_Manager::where('branch_id', $request->branch_id)->first();
    
            if ($branchManager) {
                $branchManager->update($validator->validated());
            }
    
            // Send notification
            $notificationStatus = $this->sendBranchUpdatedNotification($branch);
    
            return response()->json([
                'success' => true,
                'message' => 'Branch updated successfully',
                'notification_status' => $notificationStatus
            ], 200);
    
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating the branch',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    

    // public function deleteBranch(Request $request)
    // {
    //   $validator =Validator::make($request->all(),[
    //     'branch_id'=>'required|numeric',
    // ]);

    // if ($validator->fails()){
    //   return response()->json([
    //   'success' => false,
    //   'message' => $validator->errors()->toJson()
    //   ],400);
    //   }

    //     $branch = Branch::find($request->branch_id)->delete();
    //     $branchManager = Branch_Manager::where('branch_id', $request->branch_id)->delete();

    //     return response()->json([
    //       'success' => true ,
    //       'msg'=>'Branch has been deleted'], 200) ;
    // }
    public function deleteBranch(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'branch_id' => 'required|numeric',
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->toJson()
            ], 400);
        }
    
        try {
            $branch = Branch::find($request->branch_id);
    
            if ($branch) {
                $branch->delete();
                    Branch_Manager::where('branch_id', $request->branch_id)->delete();
    
                return response()->json([
                    'success' => true,
                    'message' => 'Branch has been deleted'
                ], 200);
            }
    
            return response()->json([
                'success' => false,
                'message' => 'Branch not found'
            ], 404);
    
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while deleting the branch',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    

    public function GetBranches()
    {
        try {
            $branches = Branch::select('id', 'address', 'desk')->paginate(10);
                if ($branches->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No branches found'
                ], 200);
            }
                return response()->json([
                'success' => true,
                'data' => $branches,
                'message' => 'Branches retrieved successfully.'
            ], 200);
    
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve branches.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    //!N Added this
    public function GetBranchById($id)
    {
        try {
            $branch = Branch::select('id', 'opening_date', 'desk', 'address', 'phone')
                ->where('id', $id)
                ->first();
    
            if (!$branch) {
                return response()->json([
                    'success' => false,
                    'message' => 'Branch not found'
                ], 404);
            }
    
            $trips = Trip::where('branch_id', $branch->id)->with('driver')->get();
    
            $branchData = [
                'opening_date' => $branch->opening_date,
                'desk' => $branch->desk,
                'address' => $branch->address,
                'phone' => $branch->phone,
                'trips' => $trips->map(function($trip) {
                    return [
                        'date' => $trip->date,
                        'number' => $trip->number,
                        'driver_name' => $trip->driver ? $trip->driver->name : null,
                    ];
                })
            ];
    
            return response()->json([
                'success' => true,
                'data' => $branchData,
                'message' => 'Branch retrieved successfully.'
            ], 200);
    
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve branch.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    //!N Added this
    public function getTrucksByBranch(Request $request)
    {
        try {
            $user = Auth::guard('admin')->user();
    
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized'
                ], 401);
            }
    
            $branch_id = $request->query('branch_id');
    
            if (!$branch_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'branch_id query parameter is required'
                ], 400);
            }
    
            $branch = Branch::find($branch_id);
    
            if (!$branch) {
                return response()->json([
                    'success' => false,
                    'message' => 'Branch not found'
                ], 404);
            }
    
            $trucks = $branch->trucks;
    
            return response()->json([
                'success' => true,
                'message' => 'Trucks retrieved successfully',
                'data' => $trucks
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while retrieving the trucks',
                'error' => $e->getMessage()
            ], 500);
        }
    }
//!N Added this
    public function getArchivedEmployeeByBranch(Request $request)
    {
        try {
            $user = Auth::guard('admin')->user();
            $branchId = $user->branch_id;
    
            $deletedEmployees = Employee::where('branch_id', $branchId)
                ->onlyTrashed()
                ->get();
    
            if ($deletedEmployees->isEmpty()) {
                return response()->json([
                    'success' => true,
                    'message' => 'No deleted employees found for the branch'
                ], 200);
            }
    
            return response()->json([
                'success' => true,
                'data' => $deletedEmployees
            ], 200);
    
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while fetching deleted employees',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
