<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DestinationController extends Controller
{
    private $destinations = [
        ['id' => 1, 'name' => 'Damascus'],
        ['id' => 2, 'name' => 'Aleppo'],
        ['id' => 3, 'name' => 'Homs'],
        ['id' => 4, 'name' => 'Latakia'],
        ['id' => 5, 'name' => 'Hama'],
        ['id' => 6, 'name' => 'Raqqa'],
        ['id' => 7, 'name' => 'Deir ez-Zor'],
        ['id' => 8, 'name' => 'Idlib'],
        ['id' => 9, 'name' => 'Hasakah'],
        ['id' => 10, 'name' => 'Qamishli'],
        ['id' => 11, 'name' => 'Daraa'],
        ['id' => 12, 'name' => 'Suwayda'],
        ['id' => 13, 'name' => 'Tartus'],
        ['id' => 14, 'name' => 'Palmyra'],
    ];

    public function getAllDestinations()
    {
        return response()->json([
            'success' => true,
            'data' => $this->destinations,
            'message' => 'Destinations retrieved successfully.'
        ], 200);
    }

    // public function getDestinationName($id)
    // {
    //     $destination = collect($this->destinations)->firstWhere('id', $id);

    //     if ($destination) {
    //         return response()->json([
    //             'success' => true,
    //             'data' => $destination,
    //             'message' => 'Destination retrieved successfully.'
    //         ], 200);
    //     }

    //     return response()->json([
    //         'success' => false,
    //         'message' => 'Destination not found.'
    //     ], 404);
    // }
}
