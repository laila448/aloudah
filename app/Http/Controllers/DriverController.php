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
            'data' => $driver
        ], 200);


    }


    public function GetProfile()
    {
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
            'data' => $driver
        ], 200);


    }
    public function GetMyTrips()
    {
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
            'data' => $driver
        ], 200);


    }

}
