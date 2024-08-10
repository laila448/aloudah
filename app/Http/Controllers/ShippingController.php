<?php

namespace App\Http\Controllers;

use App\Models\Shipping;
use App\Models\Manifest;
use App\Models\Price;
use App\Models\Trip;

use App\Models\Branch;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ShippingController extends Controller
{
  //!N Added it
    public function AddInvoice(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'source_id' => 'required|numeric|exists:branches,id',
                'destination_id' => 'required|numeric|exists:branches,id',
                'manifest_number' => 'required|string',
                'sender' => 'required|string',
                'receiver' => 'required|string',
                'sender_number' => 'required|max:15',
                'receiver_number' => 'required|max:15',
                'num_of_packages' => 'required|numeric',
                'type_id' => 'required|numeric',
                'weight' => 'required|numeric',
                'size' => 'required|string',
                'content' => 'required|string',
                'marks' => 'required|string',
                'notes' => 'string|nullable',
                'shipping_cost' => 'numeric|nullable',
                'against_shipping' => 'numeric|nullable',
                'adapter' => 'numeric|nullable',
                'advance' => 'numeric|nullable',
                'miscellaneous' => 'numeric|nullable',
                'prepaid' => 'numeric|nullable',
                'discount' => 'numeric|nullable',
                'collection' => 'numeric|nullable',
                'quantity' => 'numeric|nullable'
            ]);
    
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->toJson()
                ], 400);
            }
    
            $employee = Auth::guard('employee')->user();
            $employeeBranchId = $employee->branch_id;
    
            // Check if the source_id matches the employee's branch_id
            if ($request->source_id != $employeeBranchId) {
                return response()->json([
                    'success' => false,
                    'message' => 'You can only add invoices for trips originating from your assigned branch.'
                ], 403);
            }
    
            // Check if source_id and destination_id are the same
            if ($request->source_id == $request->destination_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'The source and destination branches cannot be the same.'
                ], 400);
            }
    
            // Check if the status of the trip is closed
            $trip = Trip::where('branch_id', $request->source_id)
                         ->where('destination_id', $request->destination_id)
                         ->first();
    
            if (!$trip) {
                return response()->json([
                    'success' => false,
                    'message' => 'Trip not found'
                ], 404);
            }
    //!ee
            // if ($trip->status === 'closed') {
            //     return response()->json([
            //         'success' => false,
            //         'message' => 'Cannot add invoice. The status of this trip is closed.'
            //     ], 403);
            // }
    
            $shippingCost = $this->calculateShippingCost($request->type_id, $request->weight);
    
            $barcode = 'SHIP-' . uniqid() . Str::random(6);
    
            $shipping = Shipping::create([
                'source_id' => $request->source_id,
                'destination_id' => $request->destination_id,
                'manifest_number' => $request->manifest_number,
                'sender' => $request->sender,
                'receiver' => $request->receiver,
                'sender_number' => $request->sender_number,
                'receiver_number' => $request->receiver_number,
                'num_of_packages' => $request->num_of_packages,
                'price_id' => $request->type_id,
                'weight' => $request->weight,
                'size' => $request->size,
                'content' => $request->content,
                'marks' => $request->marks,
                'notes' => $request->notes,
                'shipping_cost' => $shippingCost,
                'against_shipping' => $request->against_shipping,
                'adapter' => $request->adapter,
                'advance' => $request->advance,
                'miscellaneous' => $request->miscellaneous,
                'prepaid' => $request->prepaid,
                'discount' => $request->discount,
                'collection' => $request->collection,
                'barcode' => $barcode,
                'quantity' => $request->quantity
            ]);
    
            $shipping->number = $shipping->id;
            $shipping->save();
    
            $manifest = Manifest::where('number', $request->manifest_number)->first();
    
            if ($manifest) {
                $manifest->general_total += $shippingCost;
                $manifest->save();
            }
    
            return response()->json([
                'success' => true,
                'message' => 'Invoice added successfully',
                'data' => $shipping
            ], 200);
    
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while adding the invoice',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
//!Mark:Changed here

    public function getManifestWithInvoices($manifestNumber)
    {
        try {
            $manifest = Manifest::with('shippings')->where('number', $manifestNumber)->first();

            if (!$manifest) {
                return response()->json([
                    'success' => false,
                    'message' => 'Manifest not found',
                ], 404);
            }

            $shippings = $manifest->shippings->map(function ($shipping) {
                return $this->transformShipping($shipping);
            });

            return response()->json([
                'success' => true,
                'message' => 'Manifest retrieved successfully',
                'data' => [
                    // 'manifest' => $manifest,
                    'shippings' => $shippings
                ],
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while retrieving the manifest',
                'error' => $e->getMessage()
            ], 500);
        }
    }
//!Mark:Changed here ?? i didn't use this
    public function GetAllRceipts($destination_id)
    {
        try {
            $branch_id = Auth::guard('employee')->user()->branch_id;

            $receipts = Shipping::where([
                ['source_id', '=', $branch_id],
                ['destination_id', '=', $destination_id],
            ])->get();

            if ($receipts->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No receipts found'
                ], 404);
            }

            $transformedReceipts = $receipts->map(function ($receipt) {
                return $this->transformShipping($receipt);
            });

            return response()->json([
                'success' => true,
                'message' => 'Receipts retrieved successfully',
                'data' => $transformedReceipts
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while retrieving the receipts',
                'error' => $e->getMessage()
            ], 500);
        }
    }

   
    public function DetermineShippingPrices(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'type' => 'required|string',
                'cost' => 'required|numeric',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->toJson()
                ], 400);
            }

            // Create the new price entry
            $price = Price::create([
                'type' => $request->type,
                'cost' => $request->cost,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Price added successfully',
                'data' => $price
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while adding the price',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function EditShippingPrices(Request $request)
    {
        try {
            // Validate the request data
            $validator = Validator::make($request->all(), [
                'type' => 'required|string',
                'cost' => 'required|numeric',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->toJson()
                ], 400);
            }

            // Find the price by type
            $price = Price::where('type', $request->type)->first();

            if (!$price) {
                return response()->json([
                    'success' => false,
                    'message' => 'Type not found'
                ], 404);
            }

            // Update the price details
            $price->update($validator->validated());

            return response()->json([
                'success' => true,
                'message' => 'Price edited successfully',
                'data' => $price
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while editing the price',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function GetPricesList()
    {
        try {
            // Paginate the prices list
            $list = Price::paginate(10);

            if ($list->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No prices found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Prices retrieved successfully',
                'data' => $list
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while retrieving the prices',
                'error' => $e->getMessage()
            ], 500);
        }
    }
//!Mark:Changed here

    private function calculateShippingCost($type_id, $weight)
    {
        $price = Price::findOrFail($type_id);
        return $price->cost * $weight;
    }
//!Mark:Changed here

private function getDestinationName($id)
{
    $destination = Branch::find($id);
    return $destination ? $destination->desk : 'Unknown';
}

//!Mark:Changed here

private function getBranchName($id)
{
    $branch = Branch::find($id);
    return $branch ? $branch->desk : 'Unknown';
}

public function UpdateManifest(Request $request)
{
    try {
        // Validate the request data
        $validator = Validator::make($request->all(), [
            'manifest_id' => 'required|numeric',
            'status' => ['nullable', Rule::in(['open', 'closed', 'temporary'])],
            'misc_paid' => 'nullable|numeric',
            'shipping_cost' => 'nullable|numeric',
            'against_shipping' => 'nullable|numeric',
            'adapter' => 'nullable|numeric',
            'advance' => 'nullable|numeric',
            'discount' => 'nullable|numeric',
            'collection' => 'nullable|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->toJson()
            ], 400);
        }
        $loggedInEmployee = Auth::guard('employee')->user();

        $hasEditManifestPermission = Permission::where([
            ['employee_id', $loggedInEmployee->id],
            ['edit_manifest', 1]
        ])->exists();

        if (!$hasEditManifestPermission) {
            return response()->json([
                'success' => false,
                'message' => 'You do not have permission to edit a manifest'
            ], 403);
        }

        $manifest = Manifest::find($request->manifest_id);

        if (!$manifest) {
            return response()->json([
                'success' => false,
                'message' => 'Manifest not found'
            ], 404);
        }

        $manifest->update($validator->validated());

        if ($request->has('discount') && $request->discount !== null) {
            $manifest->net_total = $manifest->general_total * (1 - ($request->discount / 100));
            $manifest->save();
        }

        return response()->json([
            'success' => true,
            'message' => 'Manifest updated successfully',
            'data' => $manifest
        ], 200);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'An error occurred while updating the manifest',
            'error' => $e->getMessage()
        ], 500);
    }
}

//!Mark:Changed here
private function transformShipping($shipping)
    {
        return [
            'id' => $shipping->id,
            'source_id'=>$shipping->source_id,
            'source_name' => $this->getBranchName($shipping->source_id),
            'destination_id'=>$shipping->destination_id,
            'destination_name' => $this->getDestinationName($shipping->destination_id),
            'manifest_number' => $shipping->manifest_number,
            'sender' => $shipping->sender,
            'receiver' => $shipping->receiver,
            'sender_number' => $shipping->sender_number,
            'receiver_number' => $shipping->receiver_number,
            'num_of_packages' => $shipping->num_of_packages,
            'weight' => $shipping->weight,
            'size' => $shipping->size,
            'content' => $shipping->content,
            'marks' => $shipping->marks,
            'notes' => $shipping->notes,
            'shipping_cost' => $shipping->shipping_cost,
            'against_shipping' => $shipping->against_shipping,
            'adapter' => $shipping->adapter,
            'advance' => $shipping->advance,
            'miscellaneous' => $shipping->miscellaneous,
            'prepaid' => $shipping->prepaid,
            'discount' => $shipping->discount,
            'collection' => $shipping->collection,
            'barcode' => $shipping->barcode,

        ];
    }
}