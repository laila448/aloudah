<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Driver;
use App\Models\Trip;
use App\Models\Truck;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade as PDF;

class ReportController extends Controller
{
public function Reports()
{

    return view('ReportsWeb.driverreports');
}

public function generateReport(Request $request)
{
    
    $request->validate([
        'name' => 'required|string',
        'start_date' => 'required|date',
        'end_date' => 'required|date|after_or_equal:start_date',
    ]);

    $driver = Driver::where('name', $request->name)->first();

    if (!$driver) {
        return redirect()->back()->withErrors(['name' => 'سائق غير موجود']);
    }


    $trips = Trip::with(['truck', 'branch', 'destination'])
        ->where('driver_id', $driver->id)
        ->whereBetween('date', [$request->start_date, $request->end_date])
        ->get(['number as trip_number', 'date as trip_date', 'truck_id', 'branch_id', 'destination_id']);

    $reportData = $trips->map(function ($trip) {
        return [
            'trip_number' => $trip->trip_number,
            'truck_number' => $trip->truck->number ?? 'N/A', // Assuming truck has a `number` attribute
            'branch_desk' => $trip->branch->desk ?? 'N/A', // Assuming branch has a `desk` attribute
            'destination' => $trip->destination->desk ?? 'N/A', // Assuming destination has a `desk` attribute
            'trip_date' => $trip->trip_date,
        ];
    });

    return view('ReportsWeb.driverreport', compact('reportData'));
}

public function exportReportToPDF(Request $request)
{
    // Validate the input
    $request->validate([
        'name' => 'required|string',
        'start_date' => 'required|date',
        'end_date' => 'required|date|after_or_equal:start_date',
    ]);

    // Find the driver by name
    $driver = Driver::where('name', $request->name)->firstOrFail();

    // Fetch trips for the driver within the date range
    $trips = Trip::with(['truck', 'branch', 'destination'])
        ->where('driver_id', $driver->id)
        ->whereBetween('date', [$request->start_date, $request->end_date])
        ->get();

    // Prepare data for the view
    $reportData = $trips->map(function ($trip) {
        return [
            'trip_number' => $trip->number,
            'truck_number' => $trip->truck->number ?? 'N/A',
            'branch_desk' => $trip->branch->desk ?? 'N/A',
            'destination' => $trip->destination->desk ?? 'N/A',
            'trip_date' => $trip->date,
        ];
    });

  //  $pdf = PDF::loadView('ReportsWeb.driver_report_pdf', compact('reportData'));

 //   return $pdf->download('driver_report.pdf');
}

public function generateTruckReport(Request $request)
{
  
    $request->validate([
        'number' => 'required|string',
        'start_date' => 'required|date',
        'end_date' => 'required|date|after_or_equal:start_date',
    ]);

    $truck = Truck::where('number', $request->number)->first();

    if (!$truck) {
        return redirect()->back()->withErrors(['name' => 'سائق غير موجود']);
    }

    $trips = Trip::with(['driver', 'branch', 'destination'])
        ->where('truck_id', $truck->id)
        ->whereBetween('date', [$request->start_date, $request->end_date])
        ->get(['number as trip_number', 'date as trip_date', 'driver_id', 'branch_id', 'destination_id']);

    $reportData = $trips->map(function ($trip) {
        return [
            'trip_number' => $trip->trip_number,
            'driver' => $trip->driver->name ?? 'N/A', // Assuming truck has a `number` attribute
            'branch_desk' => $trip->branch->desk ?? 'N/A', // Assuming branch has a `desk` attribute
            'destination' => $trip->destination->desk ?? 'N/A', // Assuming destination has a `desk` attribute
            'trip_date' => $trip->trip_date,
        ];
    });

    return view('ReportsWeb.truckreport', compact('reportData','truck'));
}


public function TruckReports()
{
    return view('ReportsWeb.truckreports');


}


public function DestReports()
{
    $branches=Branch::all();
    return view('ReportsWeb.destreports',compact('branches'));


}

public function generateDestReport(Request $request)
{
  
    $request->validate([
        'desk_id' => 'required',
        'start_date' => 'required|date',
        'end_date' => 'required|date|after_or_equal:start_date',
    ]);

    $branch = Branch::where('id', $request->desk_id)->first();

    if (!$branch) {
        return redirect()->back()->withErrors(['name' => ' غير موجود']);
    }

    $trips = Trip::with(['driver','truck', 'branch', 'destination'])
        ->where('destination_id', $branch->id)
        ->whereBetween('date', [$request->start_date, $request->end_date])
        ->get(['number as trip_number', 'date as trip_date', 'driver_id','truck_id', 'branch_id', 'destination_id']);

    $reportData = $trips->map(function ($trip) {
        return [
            'trip_number' => $trip->trip_number,
            'driver' => $trip->driver->name ?? 'N/A', // Assuming truck has a `number` attribute
            'truck_number' => $trip->truck->number ?? 'N/A', // Assuming truck has a `number` attribute
            'branch_desk' => $trip->branch->desk ?? 'N/A', // Assuming branch has a `desk` attribute
            'destination' => $trip->destination->desk ?? 'N/A', // Assuming destination has a `desk` attribute
            'trip_date' => $trip->trip_date,
        ];
    });

    return view('ReportsWeb.destreport', compact('reportData'));
}

}
