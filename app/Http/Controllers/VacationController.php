<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Vacation;
use App\Models\Warehouse;
use App\Models\Warehouse_Manager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Exception;
use Illuminate\Support\Facades\Log;
class VacationController extends Controller
{
    private $messaging;

    public function __construct(Factory $firebase)
    {
        $serviceAccountPath = storage_path('app/firebase/firebase_credentials.json');
        $this->messaging = $firebase->withServiceAccount($serviceAccountPath)->createMessaging();
    }
    public function AddVacationForEmployee(Request $request)
    {
        try {
            // Validate the request data
            $validator = Validator::make($request->all(), [
                'employee_id' => 'required|numeric',
                'start' => 'required|date_format:Y-m-d',
                'end' => 'required|date_format:Y-m-d',
                'reason' => 'required|string'
            ]);
    
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->toJson()
                ], 400);
            }
    
            // Find the employee
            $employee = Employee::where('id', $request->employee_id)->first();
    
            if (!$employee) {
                return response()->json([
                    'success' => false,
                    'message' => 'Employee not found'
                ], 404);
            }
    
            // Get the branch manager
            $manager = Auth::guard('branch_manager')->user();
    
            // Create the vacation record
            $vacation = Vacation::create([
                'user_id' => $request->employee_id,
                'user_type' => 'employee',
                'start' => $request->start,
                'end' => $request->end,
                'reason' => $request->reason,
                'created_by' => $manager->name
            ]);
    
            // Send notification
            $notificationStatus = $this->sendVacationAddedNotification($employee, $vacation);
    
            return response()->json([
                'success' => true,
                'message' => 'Vacation added successfully',
                'notification_status' => $notificationStatus
            ], 200);
    
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while adding the vacation',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    
    public function AddVacationForWhManager(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'wmanager_id' => 'required|numeric',
                'start' => 'required|date_format:Y-m-d',
                'end' => 'required|date_format:Y-m-d',
                'reason' => 'required|string'
            ]);
    
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->toJson()
                ], 400);
            }
    
            $whmanager = Warehouse_Manager::where('id', $request->wmanager_id)->first();
    
            if (!$whmanager) {
                return response()->json([
                    'success' => false,
                    'message' => 'Warehouse manager not found'
                ], 404);
            }
    
            $manager = Auth::guard('branch_manager')->user();
            $vacation = Vacation::create([
                'user_id' => $request->wmanager_id,
                'user_type' => 'warehouse_manager',
                'start' => $request->start,
                'end' => $request->end,
                'reason' => $request->reason,
                'created_by' => $manager->name
            ]);
    
            // Send notification
            $notificationStatus = $this->sendVacationAddedNotification($whmanager, $vacation);
    
            return response()->json([
                'success' => true,
                'message' => 'Vacation added successfully',
                'notification_status' => $notificationStatus
            ], 200);
    
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while adding the vacation',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    

    private function sendVacationAddedNotification($user, $vacation)
{
    $title = 'Vacation Approved';
    $body = "Your vacation from {$vacation->start} to {$vacation->end} for reason '{$vacation->reason}' has been approved.";

    $deviceToken = $user->device_token;

    if ($deviceToken) {
        $message = CloudMessage::withTarget('token', $deviceToken)
            ->withNotification(Notification::create($title, $body));

        try {
            $this->messaging->send($message);
            Log::info('Notification sent: Vacation Added', ['user_id' => $user->id, 'vacation_id' => $vacation->id]);
            return 'Notification sent successfully';
        } catch (Exception $e) {
            Log::error('Failed to send FCM message: ' . $e->getMessage(), ['user_id' => $user->id, 'vacation_id' => $vacation->id]);
            return 'Failed to send notification';
        }
    } else {
        Log::warning('User device token not found, notification not sent.', ['user_id' => $user->id]);
        return 'User device token not found';
    }
}

        public function GetEmployeeVacation($id)
    {
        try {
            $employee = Employee::where('id', $id)->first();
            if (!$employee) {
                return response()->json([
                    'success' => false,
                    'message' => 'Employee not found'
                ], 404);
            }
                $vacations = Vacation::where('user_type', 'employee')
                                ->where('user_id', $id)
                                ->get();
    
            if ($vacations->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No vacations found'
                ], 404);
            }
    
            return response()->json([
                'success' => true,
                'data' => $vacations
            ], 200);
    
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while retrieving vacations',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    public function GetWhManagerVacation($id)
{
    try {
        $wmanager = Warehouse_Manager::where('id', $id)->first();

        if (!$wmanager) {
            return response()->json([
                'success' => false,
                'message' => 'Warehouse manager not found'
            ], 404);
        }
        $vacations = Vacation::where('user_type', 'warehouse_manager')
                            ->where('user_id', $id)
                            ->get();

        if ($vacations->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No vacations found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $vacations
        ], 200);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'An error occurred while retrieving vacations',
            'error' => $e->getMessage()
        ], 500);
    }
}

}
