<?php

namespace App\Http\Controllers;

use App\Models\Driver;
use App\Models\Trip;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Exception;
use Illuminate\Support\Facades\Log;
class DriverController extends Controller
{
    private $messaging;

    public function __construct(Factory $firebase)
    {
        $serviceAccountPath = storage_path('app/firebase/firebase_credentials.json');
        $this->messaging = $firebase->withServiceAccount($serviceAccountPath)->createMessaging();
    }

    public function GetDriverTrips($id)
    {
        try{
        $driver = Trip::select('number','date')->where('driver_id',$id)->paginate(10);
        if (!$driver) {
            return response()->json([
                'success' => false,
                'message' => 'Driver not found'
            ], 404);
        }
    
        return response()->json([
            'success' => true,
            'data' => $driver ,
            'message' => 'Driver trips retrieved successfully.'
        ], 200);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to retrieve driver trips.',
            'error' => $e->getMessage()
        ], 500);
    }


    }


    public function GetProfile()
    {
        try{
        $id= Auth::guard('driver')->user()->id;
        $driver = Driver::select('name','phone_number','address','employment_date')->where('id',$id)->get();
        if (!$driver) {
            return response()->json([
                'success' => false,
                'message' => ' not found'
            ], 404);
        }
    
        return response()->json([
            'success' => true,
            'data' => $driver ,
            'message' => 'Driver profile retrieved successfully.'
        ], 200);

    }  catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to retrieve driver profile.',
            'error' => $e->getMessage()
        ], 500);
    }


    }
    //!done Updated this 
    public function GetMyTrips()
    {
        try {
            $id = Auth::guard('driver')->user()->id;
            $closedTrips = Trip::select('number', 'date', 'branch_id')
                                ->where('driver_id', $id)
                                ->where('status', 'closed')
                                ->paginate(10);
    
            if ($closedTrips->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'You don\'t have any closed trips yet'
                ], 404);
            }
    
            return response()->json([
                'success' => true,
                'data' => $closedTrips,
                'message' => 'Your closed trips retrieved successfully.'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve your trips.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    

    public function GetAllDrivers()
    {
        try {
            $drivers = Driver::select('id', 'name', 'phone_number', 'address', 'employment_date')->get();

            if ($drivers->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No drivers found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $drivers,
                'message' => 'Drivers retrieved successfully.'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve drivers.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function getLocation(Request $request)
    {
        try {
            $request->validate([
                'trip_number' => 'required|string',
            ]);
    
            $trip = Trip::where('number', $request->trip_number)->where('status', 'active')->first();
    
            if (!$trip) {
                return response()->json([
                    'success' => false,
                    'message' => 'Trip not found or is not active'
                ], 404);
            }
    
            $driver = $trip->driver;
    
            if (!$driver) {
                return response()->json([
                    'success' => false,
                    'message' => 'Driver not found for this trip'
                ], 404);
            }
    
            $employee = Auth::guard('employee')->user();
    
            $notificationStatus = $this->sendLocationRetrievedNotification($employee, $driver);
    
            return response()->json([
                'success' => true,
                'data' => [
                    'current_lat' => (float) $driver->current_lat,
                    'current_lng' => (float) $driver->current_lng,
                ],
                'message' => 'Location retrieved successfully.',
                'notification_status' => $notificationStatus
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve location.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    
    private function sendLocationRetrievedNotification($employee, $driver)
{
    $title = 'Location Retrieved';
    $body = "The location of driver '{$driver->name}' has been successfully retrieved.";

    $deviceToken = $employee->device_token;

    if ($deviceToken) {
        $message = CloudMessage::withTarget('token', $deviceToken)
            ->withNotification(Notification::create($title, $body));

        try {
            $this->messaging->send($message);
            Log::info('Notification sent: Location Retrieved', ['employee_id' => $employee->id, 'employee_name' => $employee->name]);
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

    public function updateLocation(Request $request)
    {
        try {
            $request->validate([
                'current_lat' => 'required|numeric',
                'current_lng' => 'required|numeric',
                'trip_number' => 'required|string',
            ]);
    
            $trip = Trip::where('number', $request->trip_number)->where('status', 'active')->first();
    
            if (!$trip) {
                return response()->json([
                    'success' => false,
                    'message' => 'Trip not found or is not active'
                ], 404);
            }
    
            $driver = $trip->driver;
    
            if ($driver instanceof Driver) {
                $driver->current_lat = $request->current_lat;
                $driver->current_lng = $request->current_lng;
                $driver->save();
    
                $notificationStatus = $this->sendLocationUpdatedNotification($driver);
    
                return response()->json([
                    'success' => true,
                    'message' => 'Location updated successfully.',
                    'notification_status' => $notificationStatus
                ], 200);
            } else {
                throw new \Exception('Associated driver not found.');
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update location.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    
private function sendLocationUpdatedNotification($driver)
{
    $title = 'Location Updated';
    $body = "Your location has been updated successfully.";

    $deviceToken = $driver->device_token;

    if ($deviceToken) {
        $message = CloudMessage::withTarget('token', $deviceToken)
            ->withNotification(Notification::create($title, $body));

        try {
            $this->messaging->send($message);
            Log::info('Notification sent: Location Updated', ['driver_id' => $driver->id, 'driver_name' => $driver->name]);
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
public function getDriversByBranch(Request $request)
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

        $drivers = Driver::where('branch_id', $branch_id)
            ->select('id', 'name', 'phone_number', 'address', 'employment_date')
            ->paginate(10);

        return response()->json([
            'success' => true,
            'message' => 'Drivers retrieved successfully',
            'data' => $drivers
        ], 200);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'An error occurred while retrieving the drivers',
            'error' => $e->getMessage()
        ], 500);
    }
}
}
