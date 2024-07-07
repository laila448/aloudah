<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class compliantController extends Controller
{

public function AddCompliant(Request $request)
{
    try {
        // Validate the request data
        $validator = Validator::make($request->all(), [
            'message' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->toJson()
            ], 400);
        }

        // Get the authenticated customer ID
        $customer_id = Auth::guard('customer')->user()->id;

        // Create the complaint
        $complaint = Complaint::create([
            'customer_id' => $customer_id,
            'message' => $request->message,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Complaint added successfully',
            'data' => $complaint
        ], 200);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'An error occurred while adding the complaint',
            'error' => $e->getMessage()
        ], 500);
    }
}

public function AddCompliantEmp(Request $request)
{
    try {
        // Validate the request data
        $validator = Validator::make($request->all(), [
            'message' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->toJson()
            ], 400);
        }

        // Get the authenticated employee ID
        $emp_id = Auth::guard('employee')->user()->id;

        // Create the complaint
        $complaint = Complaint::create([
            'employee_id' => $emp_id, // Assuming 'employee_id' is the correct field
            'message' => $request->message,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Complaint added successfully',
            'data' => $complaint
        ], 200);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'An error occurred while adding the complaint',
            'error' => $e->getMessage()
        ], 500);
    }
}


}