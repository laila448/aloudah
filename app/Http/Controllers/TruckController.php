<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Trip;
use App\Models\Truck;
use App\Models\Trip;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Exception;
use Illuminate\Support\Facades\Log;
class TruckController extends Controller
{
    // public function AddTruck(Request $request){

    //     $validator =Validator::make($request->all(),[
    //         'branch_id'=>'required',
    //         'number'=>'required|min:4|max:20',
    //         'line'=>'string|required',
    //         'notes'=>'string',
    //     ]);
    //         $createdby = Auth::guard('branch_manager')->user()->name;
    //         $truck=Truck::create([
    //             'branch_id'=>$request->branch_id,
    //             'number'=>$request->number,
    //             'line'=> $request->line,
    //             'notes'=>$request->notes,
    //             'created_by'=>$createdby,
    //             'adding_data'=>now()->format('Y-m-d'),
                
    //         ]);
          
    //         return response()->json([
    //             'message'=>'Truck addedd successfully',
    //         ],201);
    //     }
    public function AddTruck(Request $request)
{
    try {
        $validator = Validator::make($request->all(), [
            'branch_id' => 'required|numeric',
            'number' => 'required|min:4|max:20|string|unique:trucks,number',
            'line' => 'required|string',
            'notes' => 'string|nullable',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->toJson()
            ], 400);
        }

        $createdby = Auth::guard('branch_manager')->user();

        // Check if the branch ID in the request matches the branch manager's branch ID
        if ($request->branch_id != $createdby->branch_id) {
            return response()->json([
                'success' => false,
                'message' => 'You can only add trucks to your own branch.'
            ], 403);
        }

        $truck = Truck::create([
            'branch_id' => $request->branch_id,
            'number' => $request->number,
            'line' => $request->line,
            'notes' => $request->notes,
            'created_by' => $createdby->name,
            'adding_data' => now()->format('Y-m-d'),
        ]);

        // Send notification
        $notificationStatus = $this->sendTruckAddedNotification($createdby, $truck);

        return response()->json([
            'success' => true,
            'message' => 'Truck added successfully',
            'data' => $truck,
            'notification_status' => $notificationStatus
        ], 200);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'An error occurred while adding the truck',
            'error' => $e->getMessage()
        ], 500);
    }
}

    
    private function sendTruckAddedNotification($branchManager, $truck)
    {
        $title = 'New Truck Added';
        $body = "A new truck with number {$truck->number} has been added.";
    
        $deviceToken = $branchManager->device_token;
    
        if ($deviceToken) {
            $message = CloudMessage::withTarget('token', $deviceToken)
                ->withNotification(Notification::create($title, $body));
    
            try {
                $this->messaging->send($message);
                Log::info('Notification sent: Truck Added', ['branch_manager_id' => $branchManager->id, 'truck_id' => $truck->id]);
                return 'Notification sent successfully';
            } catch (Exception $e) {
                Log::error('Failed to send FCM message: ' . $e->getMessage(), ['branch_manager_id' => $branchManager->id, 'truck_id' => $truck->id]);
                return 'Failed to send notification';
            }
        } else {
            Log::warning('Branch Manager device token not found, notification not sent.', ['branch_manager_id' => $branchManager->id]);
            return 'Branch Manager device token not found';
        }
    }
    
    public function UpdateTruck(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'truck_id' => 'required|numeric',
                'number' => 'min:4|max:20|string|nullable',
                'line' => 'string|nullable',
                'notes' => 'string|nullable',
            ]);
    
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->toJson()
                ], 400);
            }
    
            $truck = Truck::where('id', $request->truck_id)->first();
    
            if (!$truck) {
                return response()->json([
                    'success' => false,
                    'message' => 'Truck not found'
                ], 404);
            }
    
            $edit_by = Auth::guard('branch_manager')->user();
            $truck->update(array_merge(
                $validator->validated(),
                [
                    'editing_by' => $edit_by->name,
                    'editing_date' => now()->format('Y-m-d'),
                ]
            ));
    
            // Send notification
            $notificationStatus = $this->sendTruckUpdatedNotification($edit_by, $truck);
    
            return response()->json([
                'success' => true,
                'message' => 'Truck updated successfully',
                'notification_status' => $notificationStatus
            ], 200);
    
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating the truck',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    private function sendTruckUpdatedNotification($branchManager, $truck)
{
    $title = 'Truck Updated';
    $body = "The truck with number {$truck->number} has been updated.";

    $deviceToken = $branchManager->device_token;

    if ($deviceToken) {
        $message = CloudMessage::withTarget('token', $deviceToken)
            ->withNotification(Notification::create($title, $body));

        try {
            $this->messaging->send($message);
            Log::info('Notification sent: Truck Updated', ['branch_manager_id' => $branchManager->id, 'truck_id' => $truck->id]);
            return 'Notification sent successfully';
        } catch (Exception $e) {
            Log::error('Failed to send FCM message: ' . $e->getMessage(), ['branch_manager_id' => $branchManager->id, 'truck_id' => $truck->id]);
            return 'Failed to send notification';
        }
    } else {
        Log::warning('Branch Manager device token not found, notification not sent.', ['branch_manager_id' => $branchManager->id]);
        return 'Branch Manager device token not found';
    }
}

public function DeleteTruck(Request $request)
{
    try {
        $validator = Validator::make($request->all(), [
            'truck_id' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->toJson()
            ], 400);
        }

        $truck = Truck::find($request->truck_id);

        if (!$truck) {
            return response()->json([
                'success' => false,
                'message' => 'Truck not found'
            ], 404);
        }

        $truck->delete();

        $deleted_by = Auth::guard('branch_manager')->user();

        // Send notification
        $notificationStatus = $this->sendTruckDeletedNotification($deleted_by, $truck);

        return response()->json([
            'success' => true,
            'message' => 'Truck deleted successfully',
            'notification_status' => $notificationStatus
        ], 200);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'An error occurred while deleting the truck',
            'error' => $e->getMessage()
        ], 500);
    }
}

private function sendTruckDeletedNotification($branchManager, $truck)
{
    $title = 'Truck Deleted';
    $body = "The truck with number {$truck->number} has been deleted.";

    $deviceToken = $branchManager->device_token;

    if ($deviceToken) {
        $message = CloudMessage::withTarget('token', $deviceToken)
            ->withNotification(Notification::create($title, $body));

        try {
            $this->messaging->send($message);
            Log::info('Notification sent: Truck Deleted', ['branch_manager_id' => $branchManager->id, 'truck_id' => $truck->id]);
            return 'Notification sent successfully';
        } catch (Exception $e) {
            Log::error('Failed to send FCM message: ' . $e->getMessage(), ['branch_manager_id' => $branchManager->id, 'truck_id' => $truck->id]);
            return 'Failed to send notification';
        }
    } else {
        Log::warning('Branch Manager device token not found, notification not sent.', ['branch_manager_id' => $branchManager->id]);
        return 'Branch Manager device token not found';
    }
}

    public function GetTruckInformation($truck_number)
    {
        try {
            // Find the truck by number
            $truck = Truck::where('number', $truck_number)->first();
    
            if (!$truck) {
                return response()->json([
                    'success' => false,
                    'message' => 'Truck not found'
                ], 404);
            }
    
            $trips = DB::table('trips')
                ->select('number', 'date', 'driver_id')
                ->where('truck_id', $truck->id)
                ->get();
    
            $driverIds = $trips->pluck('driver_id')->unique();
    
            $drivers = DB::table('drivers')
                ->select('id', 'name')
                ->whereIn('id', $driverIds)
                ->get();
    
            $truck->trips = $trips;
            $truck->drivers = $drivers;
    
            return response()->json([
                'success' => true,
                'message' => 'Truck information retrieved successfully',
                'data' => $truck
            ], 200);
    
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while retrieving the truck information',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    public function GetTrucks()
    {
        try {
            $trucks = Truck::paginate(10);
            if ($trucks->isNotEmpty()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Trucks retrieved successfully',
                    'data' => $trucks
                ], 200);
            }
    
            return response()->json([
                'success' => false,
                'message' => 'No trucks found'
            ], 404);
    
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while retrieving the trucks',
                'error' => $e->getMessage()
            ], 500);
        }
    }
   
    public function GetTruckRecord($desk)
{
    try {
        $branch = Branch::where('desk', $desk)->first();

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

}
