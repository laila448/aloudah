<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class CustomerController extends Controller
{
    //!Here

    public function GetCustomersByName(Request $request)
    {
        try {
            // Validate the request data
            $validator = Validator::make($request->all(), [
                'name' => 'required|string'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->toJson()
                ], 400);
            }

            // Find the customers by name prefix
            $customers = Customer::where('name', 'like', $request->name . '%')->get();

            if ($customers->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No customers found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Customers retrieved successfully',
                'data' => $customers
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while retrieving the customers',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function AddCustomer(Request $request)
    {
        try {
            // Validate the request data
            $validator = Validator::make($request->all(), [
                'national_id' => 'required|max:11|unique:customers,national_id',
                'name' => 'required|string|unique:customers,name',
                'phone_number' => 'required|max:10|unique:customers,phone_number',
                'gender' => 'required|in:male,female',
                'mobile' => 'required|max:10|unique:customers,mobile',
                'address' => 'required|string',
                'address_detail' => 'required|string',
                'notes' => 'string|nullable',
            ]);
    
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->toJson()
                ], 400);
            }
    
            // Get the authenticated employee
            $employee = Auth::guard('employee')->user();
            $password = Str::random(8);
    
            // Create the customer
            $customer = Customer::create([
                'national_id' => $request->national_id,
                'name' => $request->name,
                'password' => Hash::make($password),
                'phone_number' => $request->phone_number,
                'gender' => $request->gender,
                'mobile' => $request->mobile,
                'address' => $request->address,
                'address_detail' => $request->address_detail,
                'notes' => $request->notes,
                'added_by' => $employee->name,
            ]);
    
            return response()->json([
                'success' => true,
                'message' => 'Customer added successfully',
                'data' => $customer
            ], 201);
    
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while adding the customer',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    public function UpdateCustomer(Request $request) {
        try {
            // Validate the request
            $validator = Validator::make($request->all(), [
                'customer_id' => 'required',
                'national_id' => 'max:11|nullable|unique:customers,national_id,',
                'name' => 'string|nullable|unique:customers,name,',
                'phone_number' => 'max:10|nullable|unique:customers,phone_number,',
                'gender' => 'in:male,female|nullable',
                'mobile' => 'max:10|nullable|unique:customers,mobile,',
                'address' => 'string|nullable',
                'address_detail' => 'string|nullable',
                'notes' => 'string|nullable',
            ]);
    
            // Check if validation fails
            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()->toArray()
                ], 400);
            }
    
            // Find the customer
            $customer = Customer::where('id', $request->customer_id)->first();
    
            // Check if customer exists
            if (!$customer) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Customer not found'
                ], 404);
            }
    
            // Update the customer
            $customer->update($validator->validated());
    
            // Return success response
            return response()->json([
                'status' => 'success',
                'message' => 'Customer updated successfully'
            ]);
    
        } catch (\Exception $e) {
            // Handle exceptions
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while updating the customer',
                'details' => $e->getMessage()
            ], 500);
        }
    }
    
    public function DeleteCustomer(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'customer_id' => 'required|numeric'
            ]);
    
            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->toJson()
                ], 400);
            }
    
            $customer = Customer::where('id', $request->customer_id)->first();
    
            if (!$customer) {
                return response()->json([
                    'success' => false,
                    'message' => 'Customer not found'
                ], 404);
            }
    
            $customer->delete();
    
            return response()->json([
                'success' => true,
                'message' => 'Customer deleted successfully'
            ], 200);
    
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while deleting the customer',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    public function GetCustomers()
    {
        try {
            $customers = Customer::paginate(20);
    
            if ($customers->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No customers found'
                ], 404);
            }
    
            return response()->json([
                'success' => true,
                'message' => 'Customers retrieved successfully',
                'data' => $customers
            ], 200);
    
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while retrieving the customers',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    public function GetCustomer(Request $request)
{
    try {
        $validator = Validator::make($request->all(), [
            'customer_id' => 'required|numeric'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->toJson()
            ], 400);
        }

        $customer = Customer::where('id', $request->customer_id)->first();

        if (!$customer) {
            return response()->json([
                'success' => false,
                'message' => 'Customer not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Customer retrieved successfully',
            'data' => $customer
        ], 200);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'An error occurred while retrieving the customer',
            'error' => $e->getMessage()
        ], 500);
    }
}

}
