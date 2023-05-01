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

        if (request()->query('route')) {
            $vehicles = array_filter($vehicles, fn ($val) => $val['route_id'] == request()->query('route'));
        }

        return response()->json(array_values($vehicles));
    }
}

