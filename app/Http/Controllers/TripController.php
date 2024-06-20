<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Manifest;
use App\Models\Permission;
use App\Models\Employee;
use App\Models\Price;
use App\Models\Shipping;
use App\Models\Trip;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Exception;
use Illuminate\Support\Facades\Log;
class TripController extends Controller
{

    private $messaging;

    public function __construct(Factory $firebase)
    {
        $serviceAccountPath = storage_path('app/firebase/firebase_credentials.json');
        $this->messaging = $firebase->withServiceAccount($serviceAccountPath)->createMessaging();
    }

    private $destinations = [
        ['id' => 1, 'name' => 'Damascus'],
        ['id' => 2, 'name' => 'Aleppo'],
        ['id' => 3, 'name' => 'Homs'],
        ['id' => 4, 'name' => 'Latakia'],
        ['id' => 5, 'name' => 'Hama'],
        ['id' => 6, 'name' => 'Raqqa'],
        ['id' => 7, 'name' => 'Deir ez-Zor'],
        ['id' => 8, 'name' => 'Idlib'],
        ['id' => 9, 'name' => 'Hasakah'],
        ['id' => 10, 'name' => 'Qamishli'],
        ['id' => 11, 'name' => 'Daraa'],
        ['id' => 12, 'name' => 'Suwayda'],
        ['id' => 13, 'name' => 'Tartus'],
        ['id' => 14, 'name' => 'Palmyra'],
    ];

    public function GetAllTrips()
    {
        try {
            // Paginate the trips
            $trips = Trip::paginate(10);

            if ($trips->isNotEmpty()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Trips retrieved successfully',
                    'data' => $trips
                ], 200);
            }

            return response()->json([
                'success' => false,
                'message' => 'No trips found'
            ], 404);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while retrieving the trips',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function AddTrip(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'branch_id' => 'required|numeric',
                'destination_id' => 'required|numeric',
                'truck_id' => 'required|numeric',
                'driver_id' => 'required|numeric',
            ]);
    
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->toJson()
                ], 400);
            }
    
            $branch = Branch::findOrFail($request->branch_id);
            $tripCount = Trip::where('branch_id', $branch->id)->count();
            $tripNumber = strtoupper(substr($branch->desk, 0, 2)) . '_' . $branch->id . '_' . ($tripCount + 1);
            $loggedInEmployee = Auth::guard('employee')->user();
            $hasAddTripPermission = Permission::where([
                ['employee_id', $loggedInEmployee->id],
                ['add_trip', 1]
            ])->exists();
    
            if (!$hasAddTripPermission) {
                return response()->json([
                    'success' => false,
                    'message' => 'You do not have permission to add a trip'
                ], 403);
            }
    
            $trip = new Trip();
            $trip->branch_id = $request->branch_id;
            $trip->destination_id = $request->destination_id;
            $trip->truck_id = $request->truck_id;
            $trip->driver_id = $request->driver_id;
            $trip->number = $tripNumber;
            $trip->date = now()->format('Y-m-d');
            $trip->created_by = $loggedInEmployee->name;
            $trip->manifest_id = null;
            $trip->save();
    
            $manifest = new Manifest();
            $manifest->number = $tripNumber;
            $manifest->trip_id = $trip->id;
            $manifest->save();
    
            $trip->manifest_id = $manifest->id;
            $trip->save();
    
            // Send notification
            $notificationStatus = $this->sendTripAddedNotification($loggedInEmployee, $trip);
    
            return response()->json([
                'success' => true,
                'message' => 'Trip and manifest added successfully',
                'data' => $trip,
                'notification_status' => $notificationStatus
            ], 201);
    
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while adding the trip',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    private function sendTripAddedNotification($employee, $trip)
    {
        $title = 'New Trip Added';
        $body = "A new trip with number {$trip->number} has been added.";
    
        $deviceToken = $employee->device_token;
    
        if ($deviceToken) {
            $message = CloudMessage::withTarget('token', $deviceToken)
                ->withNotification(Notification::create($title, $body));
    
            try {
                $this->messaging->send($message);
                Log::info('Notification sent: Trip Added', ['employee_id' => $employee->id, 'trip_id' => $trip->id]);
                return 'Notification sent successfully';
            } catch (Exception $e) {
                Log::error('Failed to send FCM message: ' . $e->getMessage(), ['employee_id' => $employee->id, 'trip_id' => $trip->id]);
                return 'Failed to send notification';
            }
        } else {
            Log::warning('Employee device token not found, notification not sent.', ['employee_id' => $employee->id]);
            return 'Employee device token not found';
        }
    }
    
    public function EditTrip(Request $request)
    {
        try {
            $user = Auth::guard('employee')->user();
            $validator = Validator::make($request->all(), [
                'trip_id' => 'required|numeric',
                'branch_id' => 'numeric|nullable',
                'truck_id' => 'numeric|nullable',
                'driver_id' => 'numeric|nullable',
                'manifest_id' => 'numeric|nullable',
                'trip_number' => 'string|nullable',
                'source' => 'string|nullable',
                'destination' => 'string|nullable',
                'arrival_date' => 'date|nullable',
                'status' => ['required', Rule::in(['active', 'closed', 'temporary'])]
            ]);
    
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->toJson()
                ], 400);
            }
    
            $hasEditTripPermission = Permission::where([
                ['employee_id', $user->id],
                ['edit_trip', 1]
            ])->exists();
    
            if (!$hasEditTripPermission) {
                return response()->json([
                    'success' => false,
                    'message' => 'You do not have permission to edit a trip'
                ], 403);
            }
    
            $trip = Trip::find($request->trip_id);
    
            if (!$trip) {
                return response()->json([
                    'success' => false,
                    'message' => 'Trip not found'
                ], 404);
            }
    
            $trip->update(array_merge($validator->validated(), [
                'edited_by' => $user->name,
                'editing_date' => now()->format('Y-m-d')
            ]));
    
            // Send notification
            $notificationStatus = $this->sendTripEditedNotification($user, $trip);
    
            return response()->json([
                'success' => true,
                'message' => 'Trip edited successfully',
                'notification_status' => $notificationStatus
            ], 200);
    
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while editing the trip',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    private function sendTripEditedNotification($employee, $trip)
    {
        $title = 'Trip Edited';
        $body = "The trip with number {$trip->number} has been edited.";
    
        $deviceToken = $employee->device_token;
    
        if ($deviceToken) {
            $message = CloudMessage::withTarget('token', $deviceToken)
                ->withNotification(Notification::create($title, $body));
    
            try {
                $this->messaging->send($message);
                Log::info('Notification sent: Trip Edited', ['employee_id' => $employee->id, 'trip_id' => $trip->id]);
                return 'Notification sent successfully';
            } catch (Exception $e) {
                Log::error('Failed to send FCM message: ' . $e->getMessage(), ['employee_id' => $employee->id, 'trip_id' => $trip->id]);
                return 'Failed to send notification';
            }
        } else {
            Log::warning('Employee device token not found, notification not sent.', ['employee_id' => $employee->id]);
            return 'Employee device token not found';
        }
    }
    
    public function CancelTrip(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'trip_id' => 'required|numeric',
            ]);
    
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->toJson()
                ], 400);
            }
    
            $loggedInEmployee = Auth::guard('employee')->user();
            $hasEditTripPermission = Permission::where([
                ['employee_id', $loggedInEmployee->id],
                ['edit_trip', 1]
            ])->exists();
    
            if (!$hasEditTripPermission) {
                return response()->json([
                    'success' => false,
                    'message' => 'You do not have permission to delete a trip'
                ], 403);
            }
    
            $trip = Trip::find($request->trip_id);
    
            if (!$trip) {
                return response()->json([
                    'success' => false,
                    'message' => 'Trip not found'
                ], 404);
            }
    
            $trip->delete();
    
            // Send notification
            $notificationStatus = $this->sendTripCanceledNotification($loggedInEmployee, $trip);
    
            return response()->json([
                'success' => true,
                'message' => 'Trip has been canceled',
                'notification_status' => $notificationStatus
            ], 200);
    
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while canceling the trip',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    private function sendTripCanceledNotification($employee, $trip)
    {
        $title = 'Trip Canceled';
        $body = "The trip with number {$trip->number} has been canceled.";
    
        $deviceToken = $employee->device_token;
    
        if ($deviceToken) {
            $message = CloudMessage::withTarget('token', $deviceToken)
                ->withNotification(Notification::create($title, $body));
    
            try {
                $this->messaging->send($message);
                Log::info('Notification sent: Trip Canceled', ['employee_id' => $employee->id, 'trip_id' => $trip->id]);
                return 'Notification sent successfully';
            } catch (Exception $e) {
                Log::error('Failed to send FCM message: ' . $e->getMessage(), ['employee_id' => $employee->id, 'trip_id' => $trip->id]);
                return 'Failed to send notification';
            }
        } else {
            Log::warning('Employee device token not found, notification not sent.', ['employee_id' => $employee->id]);
            return 'Employee device token not found';
        }
    }
    
    public function GetActiveTrips()
{
    try {
        $trips = Trip::with('driver:id,name', 'branch:id,address,desk', 'truck:id,number')
            ->where('status', 'active')
            ->paginate(10);

        if ($trips->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'No active trips found'
            ], 404);
        }

        // Directly access the items and map the destination name
        foreach ($trips->items() as $trip) {
            $trip->destination_name = $this->getDestinationName($trip->destination_id);
        }

        return response()->json([
            'success' => true,
            'message' => 'Active trips retrieved successfully',
            'data' => $trips
        ], 200);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'An error occurred while retrieving active trips',
            'error' => $e->getMessage()
        ], 500);
    }
}


    public function ArchiveData(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'trip_id' => 'required|numeric',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->toJson()
                ], 400);
            }

            $record = Trip::findOrFail($request->trip_id);
            $record->archived = true;
            $record->save();

            return response()->json([
                'success' => true,
                'message' => 'Trip has been archived'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while archiving the trip',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    private function getDestinationName($id)
    {
        $destination = collect($this->destinations)->firstWhere('id', $id);
        return $destination ? $destination['name'] : 'Unknown';
    }
    //!Mark:Changed this

    public function GetTripInformation($trip_number)
    {
        try {
            $trip = Trip::where('number', $trip_number)
                ->with(['driver', 'truck', 'branch', 'manifest'])
                ->first();
    
            if (!$trip) {
                return response()->json([
                    'success' => false,
                    'message' => 'Trip not found'
                ], 404);
            }
    
            $manifest = $trip->manifest;
    
            return response()->json([
                'success' => true,
                'message' => 'Trip information retrieved successfully',
                'data' => [
                    'id' => $trip->id,
                    'truck_id' => $trip->truck_id,
                    'truck_name' => $trip->truck->line, // Assuming the Truck model has a 'line' field
                    'driver_id' => $trip->driver_id,
                    'driver_name' => $trip->driver->name, // Assuming the Driver model has a 'name' field
                    'branch_id' => $trip->branch_id,
                    'branch_name' => $trip->branch->desk,
                    'manifest_id' => $trip->manifest_id,
                    'number' => $trip->number,
                    'date' => $trip->date,
                    'status' => $trip->status,
                    'arrival_date' => $trip->arrival_date,
                    'created_by' => $trip->created_by,
                    'edited_by' => $trip->edited_by,
                    'archived' => $trip->archived,
                    'destination_id' => $trip->destination_id,
                    'destination_name' => $this->getDestinationName($trip->destination_id),
                    'manifest' => $manifest ? [
                        'id' => $manifest->id,
                        'number' => $manifest->number,
                        'status' => $manifest->status,
                        'general_total' => $manifest->general_total,
                        'discount' => $manifest->discount,
                        'net_total' => $manifest->net_total,
                        'misc_paid' => $manifest->misc_paid,
                        'against_shipping' => $manifest->against_shipping,
                        'adapter' => $manifest->adapter,
                        'advance' => $manifest->advance,
                        'collection' => $manifest->collection,
                        'trip_id' => $manifest->trip_id,
                    ] : null
                ]
            ], 200);
    
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while retrieving the trip information',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    public function GetArchiveData()
    {
        try {
            $archivedRecords = Trip::with('driver:id,name', 'branch:id,address', 'truck:id,number')
                ->where('archived', true)
                ->paginate(10);

            if ($archivedRecords->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No archived trips found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Archived trips retrieved successfully',
                'data' => $archivedRecords
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while retrieving archived trips',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}