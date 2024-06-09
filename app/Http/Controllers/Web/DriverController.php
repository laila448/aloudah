<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Driver;
use Illuminate\Http\Request;

class DriverController extends Controller
{
 public function GetDrivers()
 {
  $drivers=Driver::with('branch')->get();
  return view('drivers.driverslist',compact('drivers'));


 }

 public function EditDriver(Request $request)
 {
    $id = $request->id;

    

    $driver = Driver::find($id);
    $driver->update([
        'name' => $request->name,
        'phone_number' => $request->phone_number,
        'address' => $request->address,
        'salary' => $request->salary,

    ]);

    session()->flash('edit',' Edited successffully');
    return redirect('/employee/getdrivers');


 }
 public function DeleteDriver(Request $request)
 {
    $id = $request->id;

    $branch = Driver::find($id)->delete();
    session()->flash('delete',' Deleted Successfully');
    return redirect('/employee/getdrivers');
 }



}
