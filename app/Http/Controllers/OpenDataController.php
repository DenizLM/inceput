<?php

namespace App\Http\Controllers;

use App\Console\Commands\SetVehicleNumbers;
use App\Models\Vehicle;
use App\OpenData;
use Carbon\Carbon;
use GoogleMaps\GoogleMaps;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class OpenDataController extends Controller
{
    public function __construct(public OpenData $openData)
    {
    }

    /**
     * Returns the live position of all the vehicles
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getVehiclesPositions(Request $request): JsonResponse
    {
        if ($request->has('route') && $request->has('direction')) {
            $route = $this->openData->getRouteByName($request->query('route'));
            $trip = $this->openData->getTrip($route->route_id, $request->query('direction'));
            $vehicles = $this->openData->getLocalVehicles(
                $route->route_id,
                $trip->trip_id
            );

            $shape = $this->openData->getShape($trip->shape_id);

            $vehicles = $this->mapVehiclesToRoutes($vehicles, $this->openData->getRoutes());

            $stops = $trip->stops;

            return response()->json(['shape' => $shape, 'vehicles' => $vehicles->values(), 'stops' => $stops]);
        } else {
            $vehicles = $this->openData->getLocalVehicles();

            $vehicles = $this->mapVehiclesToRoutes($vehicles, $this->openData->getRoutes());
            return response()->json(['vehicles' => $vehicles->values()]);
        }
    }

    public function mapVehiclesToRoutes(Collection $vehicles, Collection $routes)
    {
        return $vehicles->map(function ($vehicle) use ($routes) {
            $route = $routes->filter(fn($route) => $route->route_id == $vehicle->route_id)->first();
            $vehicle->route_name = $route?->route_short_name;
            $vehicle->route_color = $route?->route_color;
            return $vehicle;
        })->filter(fn($vehicle) => $vehicle->route_name);
    }

    public function getStops()
    {
        return response()->json($this->openData->getStops()->values());
    }
}

