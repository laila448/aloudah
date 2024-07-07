<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\Report;
use App\Models\Trip;
use App\Models\Truck;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ReportController extends Controller
{


    public function CreateTripReport(Request $request) {
    {
    $validator = Validator::make($request->all(), [
        'start_date' => 'required|date',
        'end_date' => 'required|date|after_or_equal:start_date',
        'report_type' => 'required|in:trips,trucks,employees',
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 400);
    }

    $reportType = $request->report_type;

    if ($reportType === 'trips') {
       
            $trips = Trip::whereBetween('date', [$request->start_date, $request->end_date])
                ->with(['source', 'destination'])
                ->get();
    
            $tripReport = $trips->map(function ($trip) {
                return [
                    'trip_number' => $trip->number,
                    'source' => [
                        'address' => $trip->source->address,
                        'branch_name' => $trip->source->desk,
                    ],
                    'destination' => [
                        'address' => $trip->destination->address,
                        'branch_name' => $trip->destination->desk,
                    ],
                    'date' => $trip->date,
                ];
            })->toArray();
            $fileName = 'trip_report_' . date('Y-m-d-H-i-s') . '.json';
            $filePath = 'reports/' . $fileName;
    
            Storage::disk('local')->put($filePath, json_encode($tripReport));
    
            $loggedInEmployee = Auth::guard('employee')->user();

        // Check if the logged-in employee has the "add_trip" permission
        $hasAddTripPermission = Permission::where([
            ['employee_id', $loggedInEmployee->id],
            ['add_report', 1]
        ])->exists();
    
        if ($hasAddTripPermission) {
            $report = Report::create([
                'file_path' => $filePath,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
            ]);
    
            return response()->json(['file_path' => $filePath]);
        } else
        return response()->json(['message' => 'You do not have permission to create a trip report'], 403);
      }
}
   

}
public function CreateTruckReport(Request $request){

    $validator = Validator::make($request->all(), [
        'start_date' => 'required|date',
        'end_date' => 'required|date|after_or_equal:start_date',
        'report_type' => 'required|in:trips,trucks,employees',
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 400);
    }

      $reportType = $request->report_type;

    if($reportType === 'trucks') {
       
            $trips = Trip::whereBetween('date', [$request->start_date, $request->end_date])
                ->with(['truck', 'driver', 'source', 'destination'])
                ->get();
    
            $truckReport = $trips
                ->groupBy('truck.number')
                ->map(function ($truckTrips, $truckNumber) {
                    $driverReports = $truckTrips->map(function ($trip) {
                        return [
                            'driver_name' => $trip->driver->name,
                            'source' => [
                                'address' => $trip->source->address,
                                'branch_name' => $trip->source->desk,
                            ],
                            'destination' => [
                                'address' => $trip->destination->address,
                                'branch_name' => $trip->destination->desk,
                            ],
                        ];
                    })->toArray();
    
                    return [
                        'truck_number' => $truckNumber,
                        'driver_reports' => $driverReports,
                    ];
                })
                ->values()
                ->toArray();
                $fileName = 'truck_report_' . date('Y-m-d-H-i-s') . '.json';
                $filePath = 'reports/' . $fileName;
                Storage::disk('local')->put($filePath, json_encode($truckReport));
                
                $loggedInEmployee = Auth::guard('employee')->user();

                $hasAddTripPermission = Permission::where([
                    ['employee_id', $loggedInEmployee->id],
                    ['edit_trip', 1]
                ])->exists();
            
                if ($hasAddTripPermission) {
                $report = Report::create([
                    'file_path' => $filePath,
                    'start_date' => $request->start_date,
                    'end_date' => $request->end_date,
                ]);
        
                return response()->json(['file_path' => $filePath]);
            } else
            return response()->json(['message' => 'You do not have permission to create a truck report'], 403);
          }
  }

  public function downloadTripReport($reportId)
  {
      $report = Report::findOrFail($reportId);
  
      $filePath = $report->file_path;
  
      $fileName = basename($filePath);
  
      if (Storage::disk('local')->exists($filePath)) {
          return response()->download(storage_path('app/' . $filePath), $fileName);
      }
  
      return response()->json(['error' => 'File not found'], 404);
  }



  public function downloadTruckReport($reportId)
{
    $report = Report::findOrFail($reportId);
    $filePath = $report->file_path;
    $fileName = basename($filePath);

    return response()->download(storage_path('app/' . $filePath), $fileName);
}




public function getTruckReports(Request $request)
{
    $reports = Report::where('file_path', 'like', '%truck%')
        ->orderByDesc('created_at')
        ->get();

    return response()->json(['reports' => $reports]);
}

public function getTripReports(Request $request)
{
    $reports = Report::where('file_path', 'like', '%trip%')
        ->orderByDesc('created_at')
        ->get();

    return response()->json(['reports' => $reports]);
}
}
