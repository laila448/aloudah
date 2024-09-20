<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Driver;
use App\Models\Trip;
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
    // Validate the input
    $request->validate([
        'name' => 'required|string',
        'start_date' => 'required|date',
        'end_date' => 'required|date|after_or_equal:start_date',
    ]);

    // Find the driver by name
    $driver = Driver::where('name', $request->name)->first();

    if (!$driver) {
        return redirect()->back()->withErrors(['name' => 'سائق غير موجود']);
    }

    // Fetch trips for the driver within the date range
    $trips = Trip::with(['truck', 'branch', 'destination'])
        ->where('driver_id', $driver->id)
        ->whereBetween('date', [$request->start_date, $request->end_date])
        ->get(['number as trip_number', 'date as trip_date', 'truck_id', 'branch_id', 'destination_id']);

    // Map the results to include truck number, branch desk, and destination
    $reportData = $trips->map(function ($trip) {
        return [
            'trip_number' => $trip->trip_number,
            'truck_number' => $trip->truck->number ?? 'N/A', // Assuming truck has a `number` attribute
            'branch_desk' => $trip->branch->desk ?? 'N/A', // Assuming branch has a `desk` attribute
            'destination' => $trip->destination->desk ?? 'N/A', // Assuming destination has a `desk` attribute
            'trip_date' => $trip->trip_date,
        ];
    });

    // Return the report view with the data
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
}
