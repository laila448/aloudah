<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Driver;
use App\Models\Manifest;
use App\Models\Permission;
use App\Models\Report;
use App\Models\Trip;
use App\Models\Truck;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Barryvdh\DomPDF\Facade\Pdf;
//use Elibyy\TCPDF\Facades\TCPDF;
use Illuminate\Support\Facades\View;
use Elibyy\TCPDF\TCPDF;

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
  
      if (Storage::disk('public')->exists($filePath)) {
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

public function DriversReport(Request $request)
{
    try{
        set_time_limit(300);

    $validator = Validator::make($request->all(),[
        'driver_name' => 'required|string',
        'from' => 'required|date_format:Y-m-d',
        'to' => 'required|date_format:Y-m-d'
    ]);

    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'message' => $validator->errors()->toJson()
        ], 400);
    }

    $driver = Driver::where('name' , $request->driver_name)->first();
    if(!$driver){
        return response()->json([
            'success' => false,
            'message' => 'Driver not found'
        ], 404);
    }
    $trips = Trip::where('driver_id' , $driver->id)
                    ->whereBetween('date' ,[$request->from , $request->to])
                    ->orderBy('date')
                    ->get();
    $trip_count = Trip::where('driver_id' , $driver->id)
    ->whereBetween('date' ,[$request->from , $request->to])
    ->count();
    foreach($trips as $trip){
        $destination = Branch::where('id' , $trip->destination_id)->first();
        $truck = Truck::where('id' , $trip->truck_id)->first();
        $branch = Branch::where('id' , $trip->branch_id)->first();
        $trip->destination = $destination->desk;
        $trip->truck_number = $truck->number;
        $trip->branch = $branch->desk;
    }
    $driver_name = $request->driver_name;
        $data = ['name' => $driver_name ,
       'trip_count' => $trip_count ,
       'from' => $request->from ,
       'to' => $request->to ,
       'trips' => $trips];
       $pdf = new TCPDF('P', 'pt', 'A4', true, 'UTF-8', false);
       $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
       $view = view('reports/drivers-report', $data);
       $html = $view->render();
     $pdf->SetTitle('drivers-report');
     $pdf->AddPage();
     $pdf->Image(public_path('logo.jpg'), '', '', 30, 30, '', '', 'T', false, 300, '', false, false, 0, false, false, false);
    $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
    $pdf->writeHTML($html ,true, false, true, false, '');
    $filename = 'drivers_report_' . now()->format('Y_m_d_H_i_s') . '.pdf';
    $storagePath = 'public/Drivers_reports/' . $filename;
    $pdf->Output(public_path('storage/Drivers_reports/'.$filename),'F');
    $pdf->Output(public_path('storage/Drivers_reports/'.$filename),'I');
    $url = Storage::url('Drivers_reports/' . $filename);

    $report = Report::create([
        'file_path' => $url,
        'start_date' => $request->from,
        'end_date' => $request->to,
    ]);

    return response()->json([
        'success' => true,
        'message' => 'Report generated successfully.',
        'data' => $report,
    ], 201); 



    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'An error occurred while generating the drivers report',
            'error' => $e->getMessage()
        ], 500);
    }
}

public function GetDriversReports(){
    try{
        $reports = Report::where('file_path' , 'like' , "%Drivers_reports%")->get();
        $reports_files = [];

        if($reports->isEmpty()){
            return response()->json([
                'success' => true,
                'message' => 'No reports found.',
            ], 200); 
        }
        foreach($reports as $report){
            $report->file = asset($report->file_path);
        }

        

        return response()->json([
            'success' => true,
            'message' => 'Drivers reports retrieved successfully. ',
            'data' => $reports
        ], 200); 

    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'An error occurred while retrieving the drivers reports',
            'error' => $e->getMessage()
        ], 500);
    }
}

public function downloadReport($reportId)
  {
      $report = Report::findOrFail($reportId);
  
      $filePath = $report->file_path;
  
      $fileName = basename($filePath);
  
      if (public_path($report->file_path)) {
          return response()->download(public_path($report->file_path), $fileName);
      }
  
      return response()->json(['error' => 'File not found'], 404);
  }

  public function TrucksReport(Request $request)
{
    try{
        set_time_limit(300);

    $validator = Validator::make($request->all(),[
        'truck_number' => 'required',
        'from' => 'required|date_format:Y-m-d',
        'to' => 'required|date_format:Y-m-d'
    ]);

    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'message' => $validator->errors()->toJson()
        ], 400);
    }

    $truck = Truck::where('number' , $request->truck_number)->first();
    if(!$truck){
        return response()->json([
            'success' => false,
            'message' => 'Truck not found'
        ], 404);
    }
    $trips = Trip::where('truck_id' , $truck->id)
                    ->whereBetween('date' ,[$request->from , $request->to])
                    ->orderBy('date')
                    ->get();
    $trip_count = Trip::where('truck_id' , $truck->id)
    ->whereBetween('date' ,[$request->from , $request->to])
    ->count();
    foreach($trips as $trip){
        $destination = Branch::where('id' , $trip->destination_id)->first();
        $driver = Driver::where('id' , $trip->driver_id)->first();
        $branch = Branch::where('id' , $trip->branch_id)->first();
        $trip->destination = $destination->desk;
        $trip->driver_name = $driver->name;
        $trip->branch = $branch->desk;
    }
    $number = $request->truck_number;
        $data = ['number' => $number ,
       'trip_count' => $trip_count ,
       'from' => $request->from ,
       'to' => $request->to ,
       'trips' => $trips];
       $pdf = new TCPDF('P', 'pt', 'A4', true, 'UTF-8', false);
       $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
       $view = view('reports/trucks-report', $data);
       $html = $view->render();
     $pdf->SetTitle('trucks-report');
     $pdf->AddPage();
     $pdf->Image(public_path('logo.jpg'), '', '', 30, 30, '', '', 'T', false, 300, '', false, false, 0, false, false, false);
    $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
    $pdf->writeHTML($html ,true, false, true, false, '');
    $filename = 'trucks_report_' . now()->format('Y_m_d_H_i_s') . '.pdf';
    $storagePath = 'public/Trucks_reports/' . $filename;
    $pdf->Output(public_path('storage/Trucks_reports/'.$filename),'F');
    $pdf->Output(public_path('storage/Trucks_reports/'.$filename),'I');
    $url = Storage::url('Trucks_reports/' . $filename);

    $report = Report::create([
        'file_path' => $url,
        'start_date' => $request->from,
        'end_date' => $request->to,
    ]);

    return response()->json([
        'success' => true,
        'message' => 'Report generated successfully.',
        'data' => $report,
    ], 201); 



    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'An error occurred while generating the drivers report',
            'error' => $e->getMessage()
        ], 500);
    }
}

public function GetTrucksReports(){
    try{
        $reports = Report::where('file_path' , 'like' , "%Trucks_reports%")->get();
        $reports_files = [];

        if($reports->isEmpty()){
            return response()->json([
                'success' => true,
                'message' => 'No reports found.',
            ], 200); 
        }
        foreach($reports as $report){
            $report->file = asset($report->file_path);
        }

        

        return response()->json([
            'success' => true,
            'message' => 'Trucks reports retrieved successfully. ',
            'data' => $reports
        ], 200); 

    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'An error occurred while retrieving the trucks reports',
            'error' => $e->getMessage()
        ], 500);
    }
}

public function DestinationsReport(Request $request)
{
    try{
        set_time_limit(300);

    $validator = Validator::make($request->all(),[
        'destination' => 'string',
        'from' => 'required|date_format:Y-m-d',
        'to' => 'required|date_format:Y-m-d',
        'status' => 'required'
    ]);

    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'message' => $validator->errors()->toJson()
        ], 400);
    }

    $trips=[];
    $destination_desk = '';
    if(!$request->exists('destination')){
       $trips = Trip::where('status' , $request->status)->get();
       $destination_desk = 'كل المدن';
    }else{
    $destination = Branch::where('desk' , $request->destination)->first();
    $destination_desk = $destination->desk;
    $trips = Trip::where('destination_id' , $destination->id)
                    ->where('status' , $request->status)
                    ->whereBetween('date' ,[$request->from , $request->to])
                    ->orderBy('date')
                    ->get();
    }
   $trip_count = $trips->count();
    if($trip_count>0){
    foreach($trips as $trip){
        $trip_destination = Branch::where('id' , $trip->destination_id)->first();
        $driver = Driver::where('id' , $trip->driver_id)->first();
        $branch = Branch::where('id' , $trip->branch_id)->first();
        $manifest = Manifest::where('number' , $trip->number)->first();
        $trip->destination = $trip_destination->desk;
        $trip->driver_name = $driver->name;
        $trip->branch = $branch->desk;
        $trip->general_total = $manifest->general_total;
        $trip->advance = $manifest->advance;
        $trip->adapter = $manifest->adapter;
        $trip->discount = $manifest->discount;
        $trip->total_disc = $manifest->general_total - (integer)$manifest->discount;
        $trip->misc_paid = $manifest->misc_paid;
        $trip->against_shipping = $manifest->against_shipping;
    }
}
  
        $data = ['branch_desk' => $destination_desk ,
       'trip_count' => $trip_count ,
       'status' => $request->status,
       'from' => $request->from ,
       'to' => $request->to ,
       'trips' => $trips];
       $pdf = new TCPDF('P', 'pt', 'A4', true, 'UTF-8', false);
       $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
       $view = view('reports/destination-report', $data);
       $html = $view->render();
     $pdf->SetTitle('destination-report');
     $pdf->AddPage();
     $pdf->Image(public_path('logo.jpg'), '', '', 30, 30, '', '', 'T', false, 300, '', false, false, 0, false, false, false);
    $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
    $pdf->writeHTML($html ,true, false, true, false, '');
    $filename = 'destination_report_' . now()->format('Y_m_d_H_i_s') . '.pdf';
    $storagePath = 'public/Destination_reports/' . $filename;
    $pdf->Output(public_path('storage/Destination_reports/'.$filename),'F');
    $pdf->Output(public_path('storage/Destination_reports/'.$filename),'I');
    $url = Storage::url('Destination_reports/' . $filename);

    $report = Report::create([
        'file_path' => $url,
        'start_date' => $request->from,
        'end_date' => $request->to,
    ]);

    return response()->json([
        'success' => true,
        'message' => 'Report generated successfully.',
        'data' => $report,
    ], 201); 



    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'An error occurred while generating the destination report',
            'error' => $e->getMessage()
        ], 500);
    }
}

public function GetDestinationReports(){
    try{
        $reports = Report::where('file_path' , 'like' , "%Destination_reports%")->get();
        $reports_files = [];

        if($reports->isEmpty()){
            return response()->json([
                'success' => true,
                'message' => 'No reports found.',
            ], 200); 
        }
        foreach($reports as $report){
            $report->file = asset($report->file_path);
        }

        

        return response()->json([
            'success' => true,
            'message' => 'Destination reports retrieved successfully. ',
            'data' => $reports
        ], 200); 

    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'An error occurred while retrieving the destination reports',
            'error' => $e->getMessage()
        ], 500);
    }
}
}
