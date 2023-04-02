<?php

namespace App\Http\Controllers;

use App\Console\Commands\SetVehicleNumbers;
use App\Models\Vehicle;
use App\OpenData;
use Carbon\Carbon;
use GoogleMaps\GoogleMaps;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CoordinatesController extends Controller
{
    public function __construct(public OpenData $openData)
    {
    }


    public function index()
    {
        $vehicles = $this->openData->getVehicles();
        $routes = $this->openData->getRoutes();

        $vehicles = array_filter(array_map(function ($vehicle) use ($routes) {
            $route = array_values(array_filter($routes, fn ($route) => $route['route_id'] == $vehicle['route_id']));

            if (!empty($route)) {
                $vehicle['route_id'] = $route[0]['route_short_name'];
            }

            return $vehicle;
        }, $vehicles), fn ($vehicle) => is_string($vehicle['route_id']));


        return response()->json(array_values($vehicles));
    }

    public function getRoute($route) {
        $route = SetVehicleNumbersphp ::ROUTES[$route];
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

