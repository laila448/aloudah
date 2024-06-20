<?php

namespace App\Http\Controllers;

use App\Mail\PasswordMail;
use App\Models\Branch;
use App\Models\Branch_Manager;
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
use Kreait\Firebase\Messaging\Notification;
use Illuminate\Support\Facades\Log;
use Exception;

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
       try{
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
       }catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to retrieve branch location.',
            'error' => $e->getMessage()
        ], 500);
    }

     }
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
 
             // Send notification
             $this->sendDoneAddedBranchNotification($branch);
 
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
 
     private function sendDoneAddedBranchNotification($branch)
    {
        $title = 'Done added branch';
        $body = "Branch '{$branch->desk}' has been successfully added.";

        $admin = Auth::guard('admin')->user();
        $deviceToken = $admin->device_token;

        if ($deviceToken) {
            $message = CloudMessage::withTarget('token', $deviceToken)
                ->withNotification(Notification::create($title, $body));

            try {
                $this->messaging->send($message);
                Log::info('Notification sent: Done added branch', ['branch_id' => $branch->id, 'admin' => $admin->name]);
                return 'Notification sent successfully';
            } catch (Exception $e) {
                Log::error('Failed to send FCM message: ' . $e->getMessage(), ['branch_id' => $branch->id, 'admin' => $admin->name]);
                return 'Failed to send notification';
            }
        } else {
            Log::warning('Admin device token not found, notification not sent.', ['admin' => $admin->name]);
            return 'Admin device token not found';
        }
    }
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

        Mail::to($branchManager->email)->send(new PasswordMail($branchManager->name, $password));

        // Send notification
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
                ->withNotification(Notification::create($title, $body));
    
            try {
                $this->messaging->send($message);
                Log::info('Notification sent: Branch Manager Added', ['branch_manager_id' => $branchManager->id, 'admin' => $admin->name]);
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
    

    private function sendBranchUpdatedNotification($branch)
{
    $title = 'Branch Updated';
    $body = "Branch '{$branch->desk}' has been successfully updated.";

    $admin = Auth::guard('admin')->user();
    $deviceToken = $admin->device_token;

    if ($deviceToken) {
        $message = CloudMessage::withTarget('token', $deviceToken)
            ->withNotification(Notification::create($title, $body));

        try {
            $this->messaging->send($message);
            Log::info('Notification sent: Branch Updated', ['branch_id' => $branch->id, 'admin' => $admin->name]);
            return 'Notification sent successfully';
        } catch (Exception $e) {
            Log::error('Failed to send FCM message: ' . $e->getMessage(), ['branch_id' => $branch->id, 'admin' => $admin->name]);
            return 'Failed to send notification';
        }
    } else {
        Log::warning('Admin device token not found, notification not sent.', ['admin' => $admin->name]);
        return 'Admin device token not found';
    }
}

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

            // Send notification
            $notificationStatus = $this->sendBranchDeletedNotification($branch);

            return response()->json([
                'success' => true,
                'message' => 'Branch has been deleted',
                'notification_status' => $notificationStatus
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

    private function sendBranchDeletedNotification($branch)
{
    $title = 'Branch Deleted';
    $body = "Branch '{$branch->desk}' has been successfully deleted.";

    $admin = Auth::guard('admin')->user();
    $deviceToken = $admin->device_token;

    if ($deviceToken) {
        $message = CloudMessage::withTarget('token', $deviceToken)
            ->withNotification(Notification::create($title, $body));

        try {
            $this->messaging->send($message);
            Log::info('Notification sent: Branch Deleted', ['branch_id' => $branch->id, 'admin' => $admin->name]);
            return 'Notification sent successfully';
        } catch (Exception $e) {
            Log::error('Failed to send FCM message: ' . $e->getMessage(), ['branch_id' => $branch->id, 'admin' => $admin->name]);
            return 'Failed to send notification';
        }
    } else {
        Log::warning('Admin device token not found, notification not sent.', ['admin' => $admin->name]);
        return 'Admin device token not found';
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
    
            // Load trips and drivers separately
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

  
}
