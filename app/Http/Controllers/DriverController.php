<?php

namespace App\Http\Controllers;

use App\Models\Driver;
use App\Models\Trip;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DriverController extends Controller
{
    

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
    public function GetMyTrips()
    {
        try{
        $id= Auth::guard('driver')->user()->id;
        $driver = Trip::select('number','date','branch_id')->where('driver_id',$id)->paginate(10);
        if (!$driver) {
            return response()->json([
                'success' => false,
                'message' => '  you dont have any trips yet'
            ], 404);
        }
    
        return response()->json([
            'success' => true,
            'data' => $driver ,
            'message' => 'your trips retrieved successfully.'
        ], 200);

    }catch (\Exception $e) {
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
            // Validate the request input
            $request->validate([
                'trip_number' => 'required|string',
            ]);
    
            // Find the trip by number
            $trip = Trip::where('number', $request->trip_number)->where('status', 'active')->first();
    
            if (!$trip) {
                return response()->json([
                    'success' => false,
                    'message' => 'Trip not found or is not active'
                ], 404);
            }
    
            // Get the associated driver
            $driver = $trip->driver;
    
            if (!$driver) {
                return response()->json([
                    'success' => false,
                    'message' => 'Driver not found for this trip'
                ], 404);
            }
    
            return response()->json([
                'success' => true,
                'data' => [
                    'current_lat' => (float) $driver->current_lat,
                    'current_lng' => (float) $driver->current_lng,
                ],
                'message' => 'Location retrieved successfully.',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve location.',
                'error' => $e->getMessage(),
            ], 500);
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

        // Find the trip by number
        $trip = Trip::where('number', $request->trip_number)->where('status', 'active')->first();

        if (!$trip) {
            return response()->json([
                'success' => false,
                'message' => 'Trip not found or is not active'
            ], 404);
        }

        // Get the associated driver
        $driver = $trip->driver;

        if ($driver instanceof Driver) {
            $driver->current_lat = $request->current_lat;
            $driver->current_lng = $request->current_lng;
            $driver->save();

            return response()->json([
                'success' => true,
                'message' => 'Location updated successfully.',
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

}
