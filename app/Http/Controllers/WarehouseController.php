<?php

namespace App\Http\Controllers;

use App\Mail\PasswordMail;
use App\Models\Warehouse;
use App\Models\Warehouse_Manager;
use Dotenv\Parser\Value;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Exception;
use Illuminate\Support\Facades\Log;
class WarehouseController extends Controller
{
    private $messaging;

    public function __construct(Factory $firebase)
    {
        $serviceAccountPath = storage_path('app/firebase/firebase_credentials.json');
        $this->messaging = $firebase->withServiceAccount($serviceAccountPath)->createMessaging();
    }
   public function addWarehouse(Request $request)
{
    try {
        $validator = Validator::make($request->all(), [               
            'warehouse_address' => 'required|string',
            'branch_id' => 'required|numeric',
            'warehouse_name' => 'required|string',
            'area' => 'required|string',
            'notes' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->toJson()
            ], 400);
        }

        $warehouse = new Warehouse();
        $warehouse->address = $request->input('warehouse_address'); 
        $warehouse->branch_id = $request->input('branch_id');
        $warehouse->warehouse_name = $request->input('warehouse_name');  
        $warehouse->area = $request->input('area'); 
        $warehouse->notes = $request->input('notes'); 
        $warehouse->save();

        // Send notification
        $manager = Auth::guard('admin')->user();
        $notificationStatus = $this->sendWarehouseAddedNotification($manager, $warehouse);

        return response()->json([
            'success' => true,
            'message' => 'Warehouse added successfully',
            'notification_status' => $notificationStatus
        ], 200);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'An error occurred while adding the warehouse',
            'error' => $e->getMessage()
        ], 500);
    }
}
private function sendWarehouseAddedNotification($manager, $warehouse)
{
    $title = 'New Warehouse Added';
    $body = "A new warehouse named {$warehouse->warehouse_name} has been added.";

    $deviceToken = $manager->device_token;

    if ($deviceToken) {
        $message = CloudMessage::withTarget('token', $deviceToken)
            ->withNotification(Notification::create($title, $body));

        try {
            $this->messaging->send($message);
            Log::info('Notification sent: Warehouse Added', ['manager_id' => $manager->id, 'warehouse_id' => $warehouse->id]);
            return 'Notification sent successfully';
        } catch (Exception $e) {
            Log::error('Failed to send FCM message: ' . $e->getMessage(), ['manager_id' => $manager->id, 'warehouse_id' => $warehouse->id]);
            return 'Failed to send notification';
        }
    } else {
        Log::warning('Manager device token not found, notification not sent.', ['manager_id' => $manager->id]);
        return 'Manager device token not found';
    }
}

public function AddWarehouseManager(Request $request)
{
    try {
        $validator = Validator::make($request->all(), [                  
            'warehouse_id' => 'required|numeric',
            'national_id' => 'required|max:11|unique:warehouse_managers,national_id',
            'manager_name' => 'required|string',
            'email' => 'required|email|unique:warehouse_managers,email',
            'phone_number' => 'required|unique:warehouse_managers,phone_number',
            'gender' => 'required|in:male,female',
            'mother_name' => 'required|string',
            'date_of_birth' => 'required|date_format:Y-m-d',
            'manager_address' => 'required|string',
            'salary' => 'required|numeric',
            'rank' => ['required', Rule::in(['warehouse_manager'])],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->toJson()
            ], 400);
        }

        $password = Str::random(8);
        $warehouseManager = new Warehouse_Manager();
        $warehouseManager->warehouse_id = $request->input('warehouse_id');
        $warehouseManager->national_id = $request->input('national_id');
        $warehouseManager->name = $request->input('manager_name');
        $warehouseManager->email = $request->input('email');
        $warehouseManager->password = Hash::make($password);
        $warehouseManager->phone_number = $request->input('phone_number');
        $warehouseManager->gender = $request->input('gender');
        $warehouseManager->mother_name = $request->input('mother_name');
        $warehouseManager->date_of_birth = $request->input('date_of_birth');
        $warehouseManager->manager_address = $request->input('manager_address');
        $warehouseManager->salary = $request->input('salary');
        $warehouseManager->rank = $request->input('rank');
        $warehouseManager->employment_date = now()->format('Y-m-d');
        $warehouseManager->save();

        if ($warehouseManager) {
            Mail::to($warehouseManager->email)->send(new PasswordMail($warehouseManager->name, $password));
        }

        // Send notification
        $manager = Auth::guard('admin')->user();
        $notificationStatus = $this->sendWarehouseManagerAddedNotification($manager, $warehouseManager);

        return response()->json([
            'success' => true,
            'message' => 'Warehouse Manager added successfully',
            'notification_status' => $notificationStatus
        ], 200);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'An error occurred while adding the warehouse manager',
            'error' => $e->getMessage()
        ], 500);
    }
}


private function sendWarehouseManagerAddedNotification($manager, $warehouseManager)
{
    $title = 'New Warehouse Manager Added';
    $body = "A new warehouse manager named {$warehouseManager->name} has been added.";

    $deviceToken = $manager->device_token;

    if ($deviceToken) {
        $message = CloudMessage::withTarget('token', $deviceToken)
            ->withNotification(Notification::create($title, $body));

        try {
            $this->messaging->send($message);
            Log::info('Notification sent: Warehouse Manager Added', ['manager_id' => $manager->id, 'warehouse_manager_id' => $warehouseManager->id]);
            return 'Notification sent successfully';
        } catch (Exception $e) {
            Log::error('Failed to send FCM message: ' . $e->getMessage(), ['manager_id' => $manager->id, 'warehouse_manager_id' => $warehouseManager->id]);
            return 'Failed to send notification';
        }
    } else {
        Log::warning('Manager device token not found, notification not sent.', ['manager_id' => $manager->id]);
        return 'Manager device token not found';
    }
}

  
public function UpdateWarehouse(Request $request)
{
    $validator = Validator::make($request->all(), [
        'address' => 'string',
        'branch' => 'string',
        'notes' => 'string',
        'area' => 'string',
        'phone' => 'numeric|min:4',
        'national_id' => 'max:11',
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

    $warehouse = Warehouse::find($request->warehouse_id);
    $warehouse->update($request->all());

    $w_Manager = Warehouse_Manager::where('warehouse_id', $request->warehouse_id)->first();
    $w_Manager->update($request->all());

    // Send notification
    $manager = Auth::guard('admin')->user();
    $notificationStatus = $this->sendWarehouseUpdatedNotification($manager, $warehouse);

    return response()->json([
        'success' => true,
        'message' => 'Warehouse updated successfully',
        'notification_status' => $notificationStatus
    ], 200);
}
private function sendWarehouseUpdatedNotification($manager, $warehouse)
{
    $title = 'Warehouse Updated';
    $body = "The warehouse named {$warehouse->warehouse_name} has been updated.";

    $deviceToken = $manager->device_token;

    if ($deviceToken) {
        $message = CloudMessage::withTarget('token', $deviceToken)
            ->withNotification(Notification::create($title, $body));

        try {
            $this->messaging->send($message);
            Log::info('Notification sent: Warehouse Updated', ['manager_id' => $manager->id, 'warehouse_id' => $warehouse->id]);
            return 'Notification sent successfully';
        } catch (Exception $e) {
            Log::error('Failed to send FCM message: ' . $e->getMessage(), ['manager_id' => $manager->id, 'warehouse_id' => $warehouse->id]);
            return 'Failed to send notification';
        }
    } else {
        Log::warning('Manager device token not found, notification not sent.', ['manager_id' => $manager->id]);
        return 'Manager device token not found';
    }
}

public function deleteWarehouse(Request $request)
{
    try {
        $validator = Validator::make($request->all(), [
            'warehouse_id' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->toJson()
            ], 400);
        }

        $warehouse = Warehouse::find($request->warehouse_id);

        if (!$warehouse) {
            return response()->json([
                'success' => false,
                'message' => 'Warehouse not found'
            ], 404);
        }

        $warehouse->delete();
        Warehouse_Manager::where('warehouse_id', $request->warehouse_id)->delete();

        // Send notification
        $manager = Auth::guard('admin')->user();
        $notificationStatus = $this->sendWarehouseDeletedNotification($manager, $warehouse);

        return response()->json([
            'success' => true,
            'message' => 'Warehouse has been deleted',
            'notification_status' => $notificationStatus
        ], 200);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'An error occurred while deleting the warehouse',
            'error' => $e->getMessage()
        ], 500);
    }
}
private function sendWarehouseDeletedNotification($manager, $warehouse)
{
    $title = 'Warehouse Deleted';
    $body = "The warehouse named {$warehouse->warehouse_name} has been deleted.";

    $deviceToken = $manager->device_token;

    if ($deviceToken) {
        $message = CloudMessage::withTarget('token', $deviceToken)
            ->withNotification(Notification::create($title, $body));

        try {
            $this->messaging->send($message);
            Log::info('Notification sent: Warehouse Deleted', ['manager_id' => $manager->id, 'warehouse_id' => $warehouse->id]);
            return 'Notification sent successfully';
        } catch (Exception $e) {
            Log::error('Failed to send FCM message: ' . $e->getMessage(), ['manager_id' => $manager->id, 'warehouse_id' => $warehouse->id]);
            return 'Failed to send notification';
        }
    } else {
        Log::warning('Manager device token not found, notification not sent.', ['manager_id' => $manager->id]);
        return 'Manager device token not found';
    }
}

    public function GetWarehouses()
    {
        try {
            $warehouses = Warehouse::paginate(10);    
            if ($warehouses->isNotEmpty()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Warehouses retrieved successfully',
                    'data' => $warehouses
                ], 200);
            }
    
            return response()->json([
                'success' => false,
                'message' => 'No warehouses found'
            ], 404);
    
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while retrieving the warehouses',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    public function GetWarehouseManager($id)
    {
        try {
            $whmanager = Warehouse_Manager::where('warehouse_id', $id)->first();    
            if ($whmanager) {
                return response()->json([
                    'success' => true,
                    'message' => 'Warehouse manager retrieved successfully',
                    'data' => $whmanager
                ], 200);
            }
    
            return response()->json([
                'success' => false,
                'message' => 'Warehouse manager not found'
            ], 404);
    
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while retrieving the warehouse manager',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
}
