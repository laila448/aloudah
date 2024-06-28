<?php

namespace App\Jobs;

use App\Models\Trip;
use App\Models\Branch;
use App\Models\Warehouse;
use App\Models\Notification as AppNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification as FirebaseNotification;

class CloseTripJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $tripId;

    public function __construct($tripId)
    {
        $this->tripId = $tripId;
    }

    public function handle()
    {
        Log::info('CloseTripJob executed at: ' . now('Asia/Damascus')->toDateTimeString());
        $trip = Trip::find($this->tripId);

        if ($trip && $trip->status === 'active') {
            $this->sendTripClosedNotification($trip);

            $trip->update(['status' => 'closed']);
            
            Log::info('Trip ID ' . $this->tripId . ' status updated to closed.');
        } elseif (!$trip) {
            Log::info('Trip ID ' . $this->tripId . ' not found.');
        } else {
            Log::info('Trip ID ' . $this->tripId . ' is not active.');
        }
    }
    private function sendTripClosedNotification($trip)
    {
        $sourceBranch = Branch::find($trip->branch_id);
        Log::info('Finding source branch', ['branch_id' => $trip->branch_id]);
    
        if ($sourceBranch) {
            Log::info('Source branch found', ['branch_id' => $sourceBranch->id]);
    
            $sourceManager = $sourceBranch->branchManager;
            if ($sourceManager) {
                Log::info('Source Branch Manager found', ['manager_id' => $sourceManager->id, 'device_token' => $sourceManager->device_token]);
    
                $this->saveNotification(
                    $sourceManager->id,
                    null,
                    'Trip Added',
                    'A new trip with number \'' . $trip->number . '\' has been added.',
                    'normal'
                );
            } else {
                Log::warning('Source branch manager not found', ['branch_id' => $trip->branch_id]);
            }
        } else {
            Log::warning('Source branch not found', ['branch_id' => $trip->branch_id]);
        }
    
        $destinationBranch = Branch::find($trip->destination_id);
        Log::info('Finding destination branch', ['destination_id' => $trip->destination_id]);
    
        if ($destinationBranch) {
            Log::info('Destination branch found', ['branch_id' => $destinationBranch->id]);
    
            $manager = $destinationBranch->branchManager;
            if ($manager) {
                Log::info('Branch Manager found', ['manager_id' => $manager->id, 'device_token' => $manager->device_token]);
    
                $this->saveNotification(
                    $manager->id,
                    null,
                    'Trip Closed',
                    'The trip with number \'' . $trip->number . '\' is on the way.',
                    'normal'
                );
    
                $this->sendFCMNotification(
                    $manager->device_token,
                    'Trip Closed',
                    'The trip with number \'' . $trip->number . '\' is on the way.'
                );
            } else {
                Log::warning('Branch manager not found for branch.', ['branch_id' => $trip->destination_id]);
            }
    
            $warehouses = Warehouse::where('branch_id', $destinationBranch->id)->get();
            if ($warehouses->count() > 0) {
                foreach ($warehouses as $warehouse) {
                    Log::info('Warehouse found', ['warehouse_id' => $warehouse->id]);
    
                    $warehouseManager = $warehouse->wmanager;
                    if ($warehouseManager) {
                        Log::info('Warehouse Manager found', ['warehouse_manager_id' => $warehouseManager->id, 'device_token' => $warehouseManager->device_token]);
    
                        $this->saveNotification(
                            null,
                            $warehouseManager->id,
                            'Trip Closed',
                            'The trip with number \'' . $trip->number . '\' is on the way.',
                            'normal'
                        );
    
                        $this->sendFCMNotification(
                            $warehouseManager->device_token,
                            'Trip Closed',
                            'The trip with number \'' . $trip->number . '\' is on the way.'
                        );
    
                        $this->saveNotification(
                            null,
                            $warehouseManager->id,
                            'Shipment Received',
                            'Did you receive the shipment with number \'' . $trip->number . '\'?',
                            'trip'
                        );
    
                        $this->sendFCMNotification(
                            $warehouseManager->device_token,
                            'Shipment Received',
                            'Did you receive the shipment with number \'' . $trip->number . '\'?',
                        );
                    } else {
                        Log::warning('Warehouse manager not found for warehouse.', ['warehouse_id' => $warehouse->id]);
                    }
                }
            } else {
                Log::warning('No warehouses found for branch.', ['branch_id' => $destinationBranch->id]);
            }
        } else {
            Log::warning('Destination branch not found, notification not sent.', ['branch_id' => $trip->destination_id]);
        }
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