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
        // $driver = Driver::with('trips')->select('trips.number')->find($id);
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
        $driver = Trip::select('number','date')->where('driver_id',$id)->paginate(10);
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

}
