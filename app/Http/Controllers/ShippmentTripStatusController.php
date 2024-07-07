<?php

namespace App\Http\Controllers;

use App\Models\Trip;
use App\Models\Branch;
use App\Models\Warehouse;
use App\Models\Notification as AppNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification as FirebaseNotification;

class ShippmentTripStatusController extends Controller
{
    public function updateTripStatus(Request $request)
    {
        $request->validate([
            'trip_number' => 'required|string',
            'status' => 'required|in:accept,reject',
        ]);

        $tripNumber = $request->input('trip_number');
        $status = $request->input('status'); // 'accept' or 'reject'

        $trip = Trip::where('number', $tripNumber)->first();

        if (!$trip || $trip->status !== 'closed') {
            return response()->json([
                'success' => false,
                'message' => 'Trip not found or not closed.',
            ], 404);
        }

        // Update the trip status
        $newStatus = $status === 'accept' ? 'accepted' : 'rejected';
        $trip->status = $newStatus;
        $trip->save();

        // Find the warehouse manager ID
        $warehouse = Warehouse::where('branch_id', $trip->destination_id)->first();
        $warehouseManagerId = $warehouse ? $warehouse->wmanager->id : null;

        if ($warehouseManagerId) {
            // Delete the notification related to the trip and warehouse manager
            $notification = AppNotification::where('warehouse_manager_id', $warehouseManagerId)
                ->where('type', 'trip')
                ->where('title', 'Shipment Received')
                ->where('body', 'Did you receive the shipment with number \'' . $trip->number . '\'?')
                ->first();

            if ($notification) {
                $notification->delete();
                Log::info('Notification deleted from database', ['notification_id' => $notification->id]);
            } else {
                Log::warning('Notification not found for deletion', ['warehouse_manager_id' => $warehouseManagerId, 'trip_number' => $tripNumber]);
            }
        } else {
            Log::warning('Warehouse manager ID not found for deletion', ['trip_number' => $tripNumber]);
        }

        // Notify the source branch manager
        $sourceBranch = Branch::find($trip->branch_id);

        if ($sourceBranch) {
            $sourceManager = $sourceBranch->branchManager;

            if ($sourceManager) {
                $notificationTitle = $status === 'accept' ? 'Shipment Accepted' : 'Shipment Rejected';
                $notificationBody = $status === 'accept'
                    ? 'The shipment with number \'' . $trip->number . '\' has been accepted by the warehouse.'
                    : 'The shipment with number \'' . $trip->number . '\' has been rejected by the warehouse.';

                $this->saveNotification(
                    $sourceManager->id,
                    null,
                    $notificationTitle,
                    $notificationBody,
                    'trip'
                );

                $this->sendFCMNotification(
                    $sourceManager->device_token,
                    $notificationTitle,
                    $notificationBody
                );
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Trip status updated successfully.',
        ], 200);
    }

    private function saveNotification($branchManagerId, $warehouseManagerId, $title, $body, $type)
    {
        try {
            $notification = AppNotification::create([
                'branch_manager_id' => $branchManagerId,
                'warehouse_manager_id' => $warehouseManagerId,
                'title' => $title,
                'body' => $body,
                'type' => $type,
                'status' => 'unread',
            ]);

            Log::info('Notification saved to database', ['notification' => $notification]);
        } catch (\Exception $e) {
            Log::error('Failed to save notification to database', ['error' => $e->getMessage()]);
        }
    }

    private function sendFCMNotification($deviceToken, $title, $body)
    {
        if ($deviceToken) {
            $message = CloudMessage::withTarget('token', $deviceToken)
                ->withNotification(FirebaseNotification::create($title, $body));

            try {
                $serviceAccountPath = storage_path('app/firebase/firebase_credentials.json');
                $messaging = (new Factory())->withServiceAccount($serviceAccountPath)->createMessaging();
                $messaging->send($message);

                Log::info('Notification sent via FCM', [
                    'device_token' => $deviceToken,
                    'title' => $title,
                    'body' => $body
                ]);
            } catch (\Exception $e) {
                Log::error('Failed to send FCM message: ' . $e->getMessage(), [
                    'device_token' => $deviceToken,
                    'title' => $title,
                    'body' => $body
                ]);
            }
        } else {
            Log::warning('Device token not found, notification not sent.', ['device_token' => $deviceToken]);
        }
    }
}
