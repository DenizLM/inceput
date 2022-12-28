<?php

namespace App\Http\Controllers;

use App\Console\Commands\SetVehicleNumbers;
use App\Models\Vehicle;
use Carbon\Carbon;
use GoogleMaps\GoogleMaps;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CoordinatesController extends Controller
{
    public function index()
    {
        $vehicles = Vehicle::all()->filter(fn ($vehicle) => $vehicle->vehicle_number !== 'undefined')->values();

        return response()->json($vehicles);
    }

    public function getRoute($route) {
        $route = SetVehicleNumbers::ROUTES[$route];
        $polyRoutes = (new GoogleMaps)->load('directions')
            ->setParam([
                'origin' => $route['origin'],
                'destination' => $route['destination'],
                'mode' => 'transit',
                'transit_routing_preference' => 'less_walking',
                'departure_time' => now()->subHour(5)->timestamp
            ])->get();


        $polyRoutes = json_decode($polyRoutes, true)['routes'][0]['overview_polyline']['points'];

        return response()->json($polyRoutes);
    }
}

