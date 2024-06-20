<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Driver;
use App\Models\Good;
use App\Models\Manifest;
use App\Models\Price;
use App\Models\Shipping;
use App\Models\Trip;
use App\Models\Truck;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Exception;
use Illuminate\Support\Facades\Log;

class GoodsController extends Controller
{
    private $messaging;

    public function __construct(Factory $firebase)
    {
        $serviceAccountPath = storage_path('app/firebase/firebase_credentials.json');
        $this->messaging = $firebase->withServiceAccount($serviceAccountPath)->createMessaging();
    }

    public function AddGood(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'barcode' => 'required|string',
            'quantity' => 'integer'
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->toJson()
            ], 400);
        }
    
        try {
            $shipping = Shipping::where('barcode', $request->barcode)->first();
    
            if (!$shipping) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invoice not found'
                ], 404);
            }
    
            $good = Good::where('barcode', $request->barcode)->first();
    
            if ($good) {
                return response()->json([
                    'success' => false,
                    'message' => 'This has already been added'
                ], 400);
            }
    
            $trip = Trip::where('number', $shipping->manifest_number)->first();
            $price = Price::select('type')->where('id', $shipping->price_id)->first();
            $truck = Truck::select('line')->where('id', $trip->truck_id)->first();
            $driver = Driver::select('name')->where('id', $trip->driver_id)->first();
            $destination = Branch::select('address')->where('id', $shipping->destination_id)->first();
            $user = Auth::guard('warehouse_manager')->user();
    
            $addGood = Good::create([
                'warehouse_id' => $user->warehouse_id,
                'type' => $price->type,
                'quantity' => $request->input('quantity'),
                'weight' => $shipping->weight,
                'size' => $shipping->size,
                'content' => $shipping->content,
                'marks' => $shipping->marks,
                'truck' => $truck->line,
                'driver' => $driver->name,
                'destination' => $destination->address,
                'ship_date' => $shipping->created_at,
                'date' => now()->format('Y-m-d'),
                'sender' => $shipping->sender,
                'receiver' => $shipping->receiver,
                'barcode' => $shipping->barcode
            ]);
    
            // Send notification
            $notificationStatus = $this->sendGoodAddedNotification($user, $addGood);
    
            return response()->json([
                'success' => true,
                'message' => 'Good has been added successfully',
                'notification_status' => $notificationStatus
            ], 200);
    
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to add this good.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    private function sendGoodAddedNotification($warehouseManager, $good)
    {
        $title = 'Good Added Successfully';
        $body = "A new good with barcode {$good->barcode} has been added to your warehouse.";
    
        $deviceToken = $warehouseManager->device_token;
    
        if ($deviceToken) {
            $message = CloudMessage::withTarget('token', $deviceToken)
                ->withNotification(Notification::create($title, $body));
    
            try {
                $this->messaging->send($message);
                Log::info('Notification sent: Good Added', ['warehouse_manager_id' => $warehouseManager->id, 'good_id' => $good->id]);
                return 'Notification sent successfully';
            } catch (Exception $e) {
                Log::error('Failed to send FCM message: ' . $e->getMessage(), ['warehouse_manager_id' => $warehouseManager->id, 'good_id' => $good->id]);
                return 'Failed to send notification';
            }
        } else {
            Log::warning('Warehouse Manager device token not found, notification not sent.', ['warehouse_manager_id' => $warehouseManager->id]);
            return 'Warehouse Manager device token not found';
        }
    }
    
    public function deleteGood(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'barcode' => 'required|string'
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->toJson()
            ], 400);
        }
    
        try {
            $good = Good::where('barcode', $request->barcode)->first();
    
            if (!$good) {
                return response()->json([
                    'success' => false,
                    'message' => 'Good not found'
                ], 404);
            }
    
            $good->delete();
    
            $user = Auth::guard('warehouse_manager')->user();
    
            // Send notification
            $notificationStatus = $this->sendGoodDeletedNotification($user, $good);
    
            return response()->json([
                'success' => true,
                'message' => 'Good has been deleted',
                'notification_status' => $notificationStatus
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete this good.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    private function sendGoodDeletedNotification($warehouseManager, $good)
    {
        $title = 'Good Deleted';
        $body = "The good with barcode {$good->barcode} has been deleted from your warehouse.";
    
        $deviceToken = $warehouseManager->device_token;
    
        if ($deviceToken) {
            $message = CloudMessage::withTarget('token', $deviceToken)
                ->withNotification(Notification::create($title, $body));
    
            try {
                $this->messaging->send($message);
                Log::info('Notification sent: Good Deleted', ['warehouse_manager_id' => $warehouseManager->id, 'good_id' => $good->id]);
                return 'Notification sent successfully';
            } catch (Exception $e) {
                Log::error('Failed to send FCM message: ' . $e->getMessage(), ['warehouse_manager_id' => $warehouseManager->id, 'good_id' => $good->id]);
                return 'Failed to send notification';
            }
        } else {
            Log::warning('Warehouse Manager device token not found, notification not sent.', ['warehouse_manager_id' => $warehouseManager->id]);
            return 'Warehouse Manager device token not found';
        }
    }
    
    public function receivingGood(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'barcode' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->toJson()
            ], 400);
        }

        try{

            $good = Good::where('barcode' , $request->barcode)->first();

            if(!$good){
                return response()->json([
                    'success' => false,
                    'message' => 'Good not found'
                ], 404); 
            }

            $good->update([
                'received' => true
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Good has been updated successfully'
            ], 200);

        }catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to change status of this good.',
            'error' => $e->getMessage()
        ], 500);
    }
    }

    public function getAllGoods(){

        try{
        $user = Auth::guard('warehouse_manager')->user(); 
        $goods = Good::where('warehouse_id' , $user->warehouse_id)
                       ->where('received' , false)
                       ->paginate(10);
        if(!$goods){
            return response()->json([
                'success' => false,
                'message' => 'No goods found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $goods,
            'message' => 'Goods retrieved successfully.'
        ], 200);
    }catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to retrieved goods.',
            'error' => $e->getMessage()
        ], 500);
    }
    }

    public function getGood(Request $request){
        $validator = Validator::make($request->all(),[
            'barcode' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->toJson()
            ], 400);
        }
        try{
            $good = Good::where('barcode' , $request->barcode)->first();
            if(!$good){
                return response()->json([
                    'success' => false,
                    'message' => 'Good not found'
                ], 404); 
            }

            return response()->json([
                'success' => true,
                'data' => $good ,
                'message' => 'Good retrieved successfully'
            ], 200); 

        }catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieved good.',
                'error' => $e->getMessage()
            ], 500);
        }

    }

    public function inventory(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'barcodes' => 'required|array',
            'barcodes.*' => 'string'
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->toJson()
            ], 400);
        }
    
        try {
            $user = Auth::guard('warehouse_manager')->user(); 
            $existingGoods = Good::where('warehouse_id', $user->warehouse_id)
                                 ->where('received', false)
                                 ->pluck('barcode')
                                 ->toArray();
            $notFound = [];
            $found = [];
    
            foreach ($existingGoods as $good) {
                if (in_array($good, $request->barcodes)) {
                    $found[] = $good;
                } else {
                    $notFound[] = $good;
                }
            }
    
            // Send notification
            $notificationStatus = $this->sendInventoryNotification($user, $found, $notFound);
    
            if (empty($notFound)) {
                return response()->json([
                    'success' => true,
                    'message' => 'Inventory process completed successfully.',
                    'notification_status' => $notificationStatus
                ], 200);
            }
    
            $notFoundGoods = Good::whereIn('barcode', $notFound)->get();
            return response()->json([
                'success' => false,
                'message' => 'Some goods are missing.',
                'data' => $notFoundGoods,
                'notification_status' => $notificationStatus
            ], 400);
    
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Inventory failed.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    private function sendInventoryNotification($warehouseManager, $found, $notFound)
    {
        if (empty($notFound)) {
            $title = 'Inventory Completed';
            $body = "The inventory process completed successfully. All goods are accounted for.";
        } else {
            $title = 'Inventory Alert';
            $body = "The inventory process found some missing goods: " . implode(', ', $notFound);
        }
    
        $deviceToken = $warehouseManager->device_token;
    
        if ($deviceToken) {
            $message = CloudMessage::withTarget('token', $deviceToken)
                ->withNotification(Notification::create($title, $body));
    
            try {
                $this->messaging->send($message);
                Log::info('Notification sent: Inventory Process', ['warehouse_manager_id' => $warehouseManager->id]);
                return 'Notification sent successfully';
            } catch (Exception $e) {
                Log::error('Failed to send FCM message: ' . $e->getMessage(), ['warehouse_manager_id' => $warehouseManager->id]);
                return 'Failed to send notification';
            }
        } else {
            Log::warning('Warehouse Manager device token not found, notification not sent.', ['warehouse_manager_id' => $warehouseManager->id]);
            return 'Warehouse Manager device token not found';
        }
    }
    
}