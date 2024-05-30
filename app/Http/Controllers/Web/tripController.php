<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Trip;
use Illuminate\Http\Request;

class tripController extends Controller
{
    public function GetTrips()
    {
        $trips=Trip::get();
        return view('trips.tripslist',compact('trips'));
    }
}
