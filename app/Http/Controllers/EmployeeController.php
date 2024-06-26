<?php

namespace App\Http\Controllers;

use App\Mail\PasswordMail;
use App\Models\Branch_Manager;
use App\Models\Driver;
use App\Models\Employee;
use App\Models\Permission;
use App\Models\Rating;
use App\Models\Warehouse_Manager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Database\QueryException;
use App\Models\Branch;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Exception;
use Illuminate\Support\Facades\Log;

class EmployeeController extends Controller
{
    private $messaging;

    public function __construct(Factory $firebase)
    {
        $serviceAccountPath = storage_path('app/firebase/firebase_credentials.json');
        $this->messaging = $firebase->withServiceAccount($serviceAccountPath)->createMessaging();
    }

    public function GetEmployees(Request $request)
    {
        try {
            $employees = Employee::paginate(10);
                if ($employees->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No employees found'
                ], 200);
            }
                return response()->json([
                'success' => true,
                'data' => $employees,
                'message' => 'Employees retrieved successfully.'
            ], 200);
    
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve employees.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    public function addDriver(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'national_id' => 'required|max:11|unique:drivers,national_id',
            'name' => 'required|min:5|max:255|unique:drivers,name',
            'email' => 'required|string|email|unique:drivers,email',
            'phone_number' => 'required|max:10|unique:drivers,phone_number',
            'gender' => 'required|in:male,female',
            'branch_id' => 'required|exists:branches,id',
            'mother_name' => 'required|string',
            'birth_date' => 'required|date_format:Y-m-d',
            'birth_place' => 'required|string',
            'mobile' => 'required|unique:drivers,mobile',
            'address' => 'required|string',
            'salary' => 'required',
            'rank' => 'required',
            'certificate' => 'required|unique:drivers,certificate'
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
            $manager = Auth::guard('branch_manager')->user();
            $driver = Driver::create(array_merge(
                $validator->validated(),
                [
                    'password' => bcrypt($password),
                    'employment_date' => now()->format('Y-m-d'),
                    'manager_name' => $manager->name
                ]
            ));
    
            if ($driver) {
                Mail::to($driver->email)->send(new PasswordMail($driver->name, $password));
    
                // Send notification
                $notificationStatus = $this->sendDriverAddedNotification($driver);
            }
    
            return response()->json([
                'success' => true,
                'message' => 'Driver added successfully',
                'notification_status' => $notificationStatus
            ], 200);
        } catch (QueryException $e) {
            $errorCode = $e->errorInfo[1];
            if ($errorCode == 1062) {
                return response()->json([
                    'success' => false,
                    'message' => 'A driver with the same National ID, Email, Phone Number, Mobile Number, or Certificate already exists. Please ensure all fields are unique.'
                ], 400);
            }
    
            return response()->json([
                'success' => false,
                'message' => 'Failed to add driver.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    private function sendDriverAddedNotification($driver)
{
    $title = 'Welcome to the Team';
    $body = "Welcome, {$driver->name}! Your account has been created successfully.";

    $deviceToken = $driver->device_token;

    if ($deviceToken) {
        $message = CloudMessage::withTarget('token', $deviceToken)
            ->withNotification(Notification::create($title, $body));

        try {
            $this->messaging->send($message);
            Log::info('Notification sent: Driver Added', ['driver_id' => $driver->id, 'driver_name' => $driver->name]);
            return 'Notification sent successfully';
        } catch (Exception $e) {
            Log::error('Failed to send FCM message: ' . $e->getMessage(), ['driver_id' => $driver->id, 'driver_name' => $driver->name]);
            return 'Failed to send notification';
        }
    } else {
        Log::warning('Driver device token not found, notification not sent.', ['driver_name' => $driver->name]);
        return 'Driver device token not found';
    }
}

public function AddEmployee(Request $request)
{
    $validator = Validator::make($request->all(), [
        'national_id' => 'required|max:11|unique:employees,national_id',
        'name' => 'required|min:5|max:255|unique:employees,name',
        'email' => 'string|email|unique:employees,email',
        'phone_number' => 'required|max:10|unique:employees,phone_number',
        'gender' => 'required|in:male,female',
        'password' => 'min:8',
        'branch_id' => 'required|exists:branches,id',
        'mother_name' => 'required|string',
        'birth_date' => 'required|date_format:Y-m-d',
        'birth_place' => 'required|string',
        'mobile' => 'required|unique:employees,mobile',
        'address' => 'required|string',
        'salary' => 'required',
        'rank' => 'required',
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
        $manager = Auth::guard('branch_manager')->user();
        $employee = Employee::create(array_merge(
            $validator->validated(),
            [
                'password' => bcrypt($password),
                'employment_date' => now()->format('Y-m-d'),
                'manager_name' => $manager->name
            ]
        ));

        if ($employee) {
            Mail::to($employee->email)->send(new PasswordMail($employee->name, $password));

            Permission::create([
                'employee_id' => $employee->id
            ]);

            // Send notification
            $notificationStatus = $this->sendEmployeeAddedNotification($employee);
        }

        return response()->json([
            'success' => true,
            'message' => 'Employee added successfully',
            'notification_status' => $notificationStatus
        ], 200);
    } catch (QueryException $e) {
        $errorCode = $e->errorInfo[1];
        if ($errorCode == 1062) {
            return response()->json([
                'success' => false,
                'message' => 'An employee with the same National ID, Email, Phone Number, or Mobile Number already exists. Please ensure all fields are unique.'
            ], 400);
        }

        return response()->json([
            'success' => false,
            'message' => 'Failed to add employee.',
            'error' => $e->getMessage()
        ], 500);
    }
}

    private function sendEmployeeAddedNotification($employee)
    {
        $title = 'Welcome to the Team';
        $body = "Welcome, {$employee->name}! Your account has been created successfully.";
    
        $deviceToken = $employee->device_token;
    
        if ($deviceToken) {
            $message = CloudMessage::withTarget('token', $deviceToken)
                ->withNotification(Notification::create($title, $body));
    
            try {
                $this->messaging->send($message);
                Log::info('Notification sent: Employee Added', ['employee_id' => $employee->id, 'employee_name' => $employee->name]);
                return 'Notification sent successfully';
            } catch (Exception $e) {
                Log::error('Failed to send FCM message: ' . $e->getMessage(), ['employee_id' => $employee->id, 'employee_name' => $employee->name]);
                return 'Failed to send notification';
            }
        } else {
            Log::warning('Employee device token not found, notification not sent.', ['employee_name' => $employee->name]);
            return 'Employee device token not found';
        }
    }


    public function UpdateEmployee(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'national_id' => 'max:11',
                'name' => 'min:5|max:255|unique:employees',
                'email' => 'string|email|unique:employees',
                'phone_number' => 'max:10',
                'gender' => 'in:male,female',
                'password' => 'min:8',
                'branch_id' => 'numeric',
                'mother_name' => 'string',
                'birth_date' => 'date_format:Y-m-d',
                'birth_place' => 'string',
                'mobile' => 'max:10',
                'address' => 'string',
                'employee_id' => 'required|numeric'
            ]);
    
            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 400);
            }
    
            $employee = Employee::where('id', $request->employee_id)->first();
    
            if (!$employee) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Employee not found'
                ], 404);
            }
    
            $employee->update(array_merge(
                $validator->validated(),
                $request->password ? ['password' => bcrypt($request->password)] : []
            ));
    
            // Send notification
            $notificationStatus = $this->sendEmployeeUpdateNotification($employee);
    
            return response()->json([
                'status' => 'success',
                'message' => 'Employee updated successfully',
                'notification_status' => $notificationStatus
            ], 200);
    
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while updating the employee',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    private function sendEmployeeUpdateNotification($employee)
    {
        $title = 'Profile Updated';
        $body = "Your profile has been successfully updated.";
    
        $deviceToken = $employee->device_token;
    
        if ($deviceToken) {
            $message = CloudMessage::withTarget('token', $deviceToken)
                ->withNotification(Notification::create($title, $body));
    
            try {
                $this->messaging->send($message);
                Log::info('Notification sent: Profile Updated', ['employee_id' => $employee->id, 'employee_name' => $employee->name]);
                return 'Notification sent successfully';
            } catch (Exception $e) {
                Log::error('Failed to send FCM message: ' . $e->getMessage(), ['employee_id' => $employee->id, 'employee_name' => $employee->name]);
                return 'Failed to send notification';
            }
        } else {
            Log::warning('Employee device token not found, notification not sent.', ['employee_name' => $employee->name]);
            return 'Employee device token not found';
        }
    }
        
    public function UpdateDriver(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'national_id' => 'max:11',
                'name' => 'min:5|max:255|unique:drivers',
                'phone_number' => 'max:10',
                'gender' => 'in:male,female',
                'certificate' => 'string',
                'branch_id' => 'numeric',
                'mother_name' => 'string',
                'birth_date' => 'date_format:Y-m-d',
                'birth_place' => 'string',
                'mobile' => 'max:10',
                'address' => 'string', 
                'driver_id' => 'required|numeric'
            ]);
    
            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 400);
            }
    
            $driver = Driver::where('id', $request->driver_id)->first();
    
            if (!$driver) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Driver not found'
                ], 404);
            }
    
            $driver->update($validator->validated());
    
            // Send notification
            $notificationStatus = $this->sendDriverUpdateNotification($driver);
    
            return response()->json([
                'status' => 'success',
                'message' => 'Driver updated successfully',
                'notification_status' => $notificationStatus
            ], 200);
    
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while updating the driver',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    private function sendDriverUpdateNotification($driver)
    {
        $title = 'Profile Updated';
        $body = "Your profile has been successfully updated.";
    
        $deviceToken = $driver->device_token;
    
        if ($deviceToken) {
            $message = CloudMessage::withTarget('token', $deviceToken)
                ->withNotification(Notification::create($title, $body));
    
            try {
                $this->messaging->send($message);
                Log::info('Notification sent: Profile Updated', ['driver_id' => $driver->id, 'driver_name' => $driver->name]);
                return 'Notification sent successfully';
            } catch (Exception $e) {
                Log::error('Failed to send FCM message: ' . $e->getMessage(), ['driver_id' => $driver->id, 'driver_name' => $driver->name]);
                return 'Failed to send notification';
            }
        } else {
            Log::warning('Driver device token not found, notification not sent.', ['driver_name' => $driver->name]);
            return 'Driver device token not found';
        }
    }
        
    public function DeleteEmployee(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'employee_id' => 'required|numeric',
            ]);
    
            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 400);
            }
                $employee = Employee::where('id', $request->employee_id)->first();
    
            if (!$employee) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Employee not found'
                ], 404);
            }
                $employee->delete();
    
            return response()->json([
                'status' => 'success',
                'message' => 'Employee has been deleted'
            ], 200);
    
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while deleting the employee',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    public function DeleteDriver(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'driver_id' => 'required|numeric',
            ]);
    
            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 400);
            }
                $driver = Driver::where('id', $request->driver_id)->first();
    
            if (!$driver) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Driver not found'
                ], 404);
            }
                $driver->delete();
    
            return response()->json([
                'status' => 'success',
                'message' => 'Driver has been deleted'
            ], 200);
    
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while deleting the driver',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    public function PromoteEmployee(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'rank' => 'required|string',
                'branch_id' => 'required|numeric',
                'employee_id' => 'required|numeric'
            ]);
    
            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation error',
                    'errors' => $validator->errors()
                ], 400);
            }
    
            $employee = Employee::where('id', $request->employee_id)->first();
    
            if (!$employee) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Employee not found'
                ], 404);
            }
    
            if ($request->rank == 'Branch_manager') {
                Branch_Manager::create([
                    'national_id' => $employee->national_id,
                    'name' => $employee->name,
                    'email' => $employee->email,
                    'password' => $employee->password,
                    'phone_number' => $employee->phone_number,
                    'branch_id' => $request->branch_id,
                    'gender' => $employee->gender,
                    'mother_name' => $employee->mother_name,
                    'date_of_birth' => $employee->birth_date,
                    'manager_address' => $employee->address,
                    'salary' => $employee->salary,
                    'rank' => $request->rank,
                    'employment_date' => now()->format('Y-m-d'),
                ]);
                $employee->delete();
    
                // Send notification
                $notificationStatus = $this->sendPromotionNotification($employee, $request->rank);
    
                return response()->json([
                    'status' => 'success',
                    'message' => 'Employee has been promoted to Branch Manager',
                    'notification_status' => $notificationStatus
                ], 200);
    
            } elseif ($request->rank == 'warehouse_manager') {
                Warehouse_Manager::create([
                    'national_id' => $employee->national_id,
                    'name' => $employee->name,
                    'email' => $employee->email,
                    'password' => $employee->password,
                    'phone_number' => $employee->phone_number,
                    'warehouse_id' => $request->branch_id,
                    'gender' => $employee->gender,
                    'mother_name' => $employee->mother_name,
                    'date_of_birth' => $employee->birth_date,
                    'manager_address' => $employee->address,
                    'salary' => $employee->salary,
                    'rank' => $request->rank,
                    'employment_date' => now()->format('Y-m-d'),
                ]);
                $employee->delete();
    
                // Send notification
                $notificationStatus = $this->sendPromotionNotification($employee, $request->rank);
    
                return response()->json([
                    'status' => 'success',
                    'message' => 'Employee has been promoted to Warehouse Manager',
                    'notification_status' => $notificationStatus
                ], 200);
    
            } else {
                $employee->update([
                    'rank' => $request->rank,
                    'branch_id' => $request->branch_id,
                ]);
    
                // Send notification
                $notificationStatus = $this->sendPromotionNotification($employee, $request->rank);
    
                return response()->json([
                    'status' => 'success',
                    'message' => 'Employee has been promoted',
                    'notification_status' => $notificationStatus
                ], 200);
            }
    
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while promoting the employee',
                'error' => $e->getMessage()
            ], 500);
        }
    }
        private function sendPromotionNotification($employee, $newRank)
{
    $title = 'Promotion Received';
    $body = "Congratulations! You have been promoted to the position of {$newRank}.";

    $deviceToken = $employee->device_token;

    if ($deviceToken) {
        $message = CloudMessage::withTarget('token', $deviceToken)
            ->withNotification(Notification::create($title, $body));

        try {
            $this->messaging->send($message);
            Log::info('Notification sent: Promotion Received', ['employee_id' => $employee->id, 'employee_name' => $employee->name]);
            return 'Notification sent successfully';
        } catch (Exception $e) {
            Log::error('Failed to send FCM message: ' . $e->getMessage(), ['employee_id' => $employee->id, 'employee_name' => $employee->name]);
            return 'Failed to send notification';
        }
    } else {
        Log::warning('Employee device token not found, notification not sent.', ['employee_name' => $employee->name]);
        return 'Employee device token not found';
    }
}

    public function RateEmployee(Request $request)
{
    try {
        $validator = Validator::make($request->all(), [
            'rate' => 'required|numeric|between:1,5',
            'employee_id' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 400);
        }

        $employee = Employee::where('id', $request->employee_id)->first();

        if (!$employee) {
            return response()->json([
                'status' => 'error',
                'message' => 'Employee not found'
            ], 404);
        }

        Rating::create([
            'rate' => $request->rate,
            'employee_id' => $request->employee_id
        ]);

        // Send notification
        $notificationStatus = $this->sendRatingNotification($employee, $request->rate);

        return response()->json([
            'status' => 'success',
            'message' => 'Rating added successfully',
            'notification_status' => $notificationStatus
        ], 200);

    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'An error occurred while adding the rating',
            'error' => $e->getMessage()
        ], 500);
    }
}
private function sendRatingNotification($employee, $rating)
{
    $title = 'New Rating Received';
    $body = "You have received a new rating of {$rating}.";

    $deviceToken = $employee->device_token;

    if ($deviceToken) {
        $message = CloudMessage::withTarget('token', $deviceToken)
            ->withNotification(Notification::create($title, $body));

        try {
            $this->messaging->send($message);
            Log::info('Notification sent: New Rating Received', ['employee_id' => $employee->id, 'employee_name' => $employee->name]);
            return 'Notification sent successfully';
        } catch (Exception $e) {
            Log::error('Failed to send FCM message: ' . $e->getMessage(), ['employee_id' => $employee->id, 'employee_name' => $employee->name]);
            return 'Failed to send notification';
        }
    } else {
        Log::warning('Employee device token not found, notification not sent.', ['employee_name' => $employee->name]);
        return 'Employee device token not found';
    }
}

public function GetAllEmployees()
{
    try {
        $branchId = Auth::guard('branch_manager')->user()->branch_id;
        $employees = Employee::where('branch_id', $branchId)->get(['id', 'name', 'email', 'phone_number']);

        return response()->json([
            'status' => 'success',
            'message' => 'Employees retrieved successfully',
            'data' => $employees
        ], 200);

    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'An error occurred while fetching the employees',
            'error' => $e->getMessage()
        ], 500);
    }
}



public function EditPermissions(Request $request)
{
    try {
        $validator = Validator::make($request->all(), [
            'employee_id' => 'required|numeric',
            'add_trip' => 'boolean',
            'edit_trip' => 'boolean',
            'delete_trip' => 'boolean',
            'drawer' => 'boolean',
            'email' => 'boolean',
            'trip_list' => 'boolean',
            'print_road' => 'boolean',
            'print_trips' => 'boolean',
            'edit_close' => 'boolean',
            'add_manifest' => 'boolean',
            'edit_manifest' => 'boolean',
            'delete_manifest' => 'boolean',
            'view_manifest' => 'boolean',
            'add_report' => 'boolean',
            'edit_report' => 'boolean',
            'delete_report' => 'boolean',
            'view_report' => 'boolean',
            'add_misc' => 'boolean',
            'edit_misc' => 'boolean',
            'delete_misc' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation error',
                'errors' => $validator->errors()
            ], 400);
        }
        $employee = Employee::where('id', $request->employee_id)->first();

        if (!$employee) {
            return response()->json([
                'status' => 'error',
                'message' => 'Employee not found'
            ], 404);
        }
        Permission::where('employee_id', $request->employee_id)->update($validator->validated());

        return response()->json([
            'status' => 'success',
            'message' => 'Permissions updated successfully'
        ], 200);

    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'An error occurred while updating permissions',
            'error' => $e->getMessage()
        ], 500);
    }
}
public function GetEmployee($id)
{
    try {
        $employee = Employee::where('id', $id)->first();

        if ($employee) {
            $employeeData = $employee->makeHidden(['password']);
            $rating =round(Rating::where('employee_id', $id)->avg('rate'),1);
            $employeeData->rating = $rating;

            return response()->json([
                'status' => 'success',
                'message' => 'Employee retrieved successfully',
                'data' => $employeeData
            ], 200);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Employee not found'
        ], 404);

    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'An error occurred while retrieving the employee',
            'error' => $e->getMessage()
        ], 500);
    }
}

public function GetBranchEmployees($id)
{
    try {
        $employees = Employee::where('branch_id', $id)->get();

        if ($employees->isNotEmpty()) {
            $employees->makeHidden(['password']);

            return response()->json([
                'status' => 'success',
                'message' => 'Employees retrieved successfully',
                'data' => $employees
            ], 200);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'No employees found for the given branch ID'
        ], 404);

    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'An error occurred while retrieving the employees',
            'error' => $e->getMessage()
        ], 500);
    }
  }

   public function GetDrivers(){

    $drivers = Driver::all();
    if($drivers){
        return response()->json([
            'success' => true ,
            'data' => $drivers
        ] , 200);
    }
    return response()->json([
        'success' => false ,
        'message' => 'No drivers found'
    ], 404);
   }

   public function GetBranchDrivers($id){
    
    $drivers = Driver::where('branch_id' , $id)->paginate(10);
    if($drivers){
        return response()->json([
            'success' => true,
            'data' => $drivers
        ],200);
    }
    return response()->json([
        'success' => false,
        'message' => 'No drivers found'
    ], 404);
   }

   public function GetDriversForMyBranch(){
        $drivers = null;
    if(Auth::guard('branch_manager')->check()){
        $user = Auth::guard('branch_manager')->user();
        $drivers = Driver::where('branch_id' , $user->branch_id)->paginate(10);
    }elseif(Auth::guard('employee')->check()){
        $user = Auth::guard('employee')->user();
        $drivers = Driver::where('branch_id' , $user->branch_id)->paginate(10);
    }
    if($drivers == null){
        return response()->json([
            'success' => false,
            'message' => 'No drivers found'
        ] , 404);
    }
    return response()->json([
        'success' => true ,
        'data' => $drivers
    ], 200);

}

private function getBranchName($id)
{
    $branch = Branch::find($id);
    return $branch ? $branch->desk : 'Unknown';
}

public function GetProfile()
{
    try {
        $id = Auth::guard('employee')->user()->id;
        $employee = Employee::select('name', 'national_id', 'email', 'phone_number', 'gender', 'branch_id', 'mother_name', 'birth_date', 'birth_place', 'mobile', 'address', 'salary', 'rank', 'employment_date', 'resignation_date', 'manager_name')
            ->where('id', $id)
            ->first();
        
        if (!$employee) {
            return response()->json([
                'success' => false,
                'message' => 'Employee not found'
            ], 404);
        }

        $employeeData = $employee->toArray();
        $employeeData['branch_name'] = $this->getBranchName($employee->branch_id);

        return response()->json([
            'success' => true,
            'data' => $employeeData,
            'message' => 'Employee profile retrieved successfully.'
        ], 200);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to retrieve employee profile.',
            'error' => $e->getMessage()
        ], 500);
    }
}
public function getEmployeesByBranch(Request $request)
{
    try {
        $user = Auth::guard('admin')->user();

        $branch_id = $request->query('branch_id');

        if (!$branch_id) {
            return response()->json([
                'success' => false,
                'message' => 'branch_id query parameter is required'
            ], 400);
        }

        $employees = Employee::where('branch_id', $branch_id)
            ->select('id','name', 'email', 'phone_number', 'manager_name', 'employment_date', 'salary', 'address')
            ->paginate(10);

        return response()->json([
            'success' => true,
            'message' => 'Employees retrieved successfully',
            'data' => $employees
        ], 200);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'An error occurred while retrieving the employees',
            'error' => $e->getMessage()
        ], 500);
    }
}
}

