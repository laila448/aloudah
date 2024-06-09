<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Manifest;
use App\Models\Shipping;
use App\Models\Trip;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class tripController extends Controller
{
    public function GetTrips()
    {
        $trips = Trip::with('driver', 'truck', 'branch', 'destination')
        ->where('status', 'active')
        ->get();
        return view('trips.tripslist',compact('trips'));
    }


    public function GetTemporaryTrips()
    {
        $trips = Trip::with('driver', 'truck', 'branch', 'destination')
        ->where('status', 'temporary')
        ->get();
        return view('trips.temporarytrips',compact('trips'));
    }

    public function GetClosedTrips()
    {
        $trips = Trip::with('driver', 'truck', 'branch', 'destination')
        ->where('status', 'closed')
        ->where('archived', 0)
        ->get();
        return view('trips.closedtrips',compact('trips'));
    }
    public function GetArchiveTrips()
    {
        $trips = Trip::with('driver', 'truck', 'branch', 'destination')
        ->where('archived', 1)
        ->get();
        return view('trips.archivedtrips',compact('trips'));
    }


    public function EditTrip(Request $request)
    {
        $id = $request->id;

        $this->validate($request, [

            'number' => '',
            'status' => '',
        ],[

        ]);

        $trip = Trip::find($id);
        $trip->update([
            'number' => $request->number,
            'status' => $request->status,
            'edited_by'=> ( Auth::guard('emp_web')->user()->name)
        ]);

        session()->flash('edit','تم التعديل  بنجاج');
        return redirect('/employee/tripslist');
    
    }
    public function ArchiveTrip(Request $request)
    {
        $id = $request->id;

         
        $this->validate($request, [

            'number' => '',
            'archived' => '',
        ],[

        ]);
        $trip = Trip::find($id);
        $trip->update([
            'number' => $request->number,
            'archived' =>(1) ,
            'edited_by'=> ( Auth::guard('emp_web')->user()->name)
        ]);

        session()->flash('edit','تم التعديل  بنجاج');
        return redirect('/employee/closedtrips');
    
    }

    public function DeleteTrip(Request $request)
    {

        $id = $request->id;
        Trip::find($id)->delete();
        session()->flash('delete','تم الحذف  بنجاح');
        return redirect('/employee/tripslist');
    }




///////////////////////manifests////////////////////////

    public function GetManifests()
    {
        $manifests = Manifest::with('trip')->orderByDesc('manifests.created_at')
        ->get();
        return view('trips.manifestlist',compact('manifests'));
    }


    public function GetManifestinformation(Request $request)
    {   
        $id=$request->id;
        $number=$request->number;
        $manifest=Manifest::find($id);
        $trip=Trip::where('number',$number)->with('driver','truck','branch','destination')->first();
        $shippings=Shipping::where('manifest_number',$number)->with('branchSource')->get();
        return view('trips.manifestinformation',compact('shippings','trip','manifest'));
    }
}
