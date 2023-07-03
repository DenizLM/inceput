<?php

namespace App\Http\Controllers;

use App\ChartsHelper;
use App\Console\Commands\SetVehicleNumbers;
use App\Models\Route;
use App\Models\Trip;
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

    public function statsIndex()
    {
        $averages = ChartsHelper::getAverageSpeedAllRoutes(2);

        $averageSpeedsDesc = array_slice($averages, 0, 10, true);

        $averageSpeedsAsc = array_reverse(array_slice($averages, -10, 10, true), true);

        $vehicles = $this->mapVehiclesToRoutes($this->openData->getLocalVehicles(), $this->openData->getRoutes());

        $bussesCount = clone($vehicles)->filter(fn($vehicle) => $vehicle->vehicle_type === '3')->countBy('route_name')->sortDesc()->take(10);
        $tramsCount = clone($vehicles)->filter(fn($vehicle) => $vehicle->vehicle_type === '0')->countBy('route_name');

        $shapes = $this->openData->getShapes()->groupBy('shape_id')->filter(fn($shape, $key) => str_ends_with($key, '0'));
        $distances = [];

        foreach ($shapes as $key => $shape) {
            $length = round($this->openData->getDistanceFromShape($shape), '2');
            $route = $this->openData->getRouteFromTrip(Trip::query()->where('shape_id', $key)->first());
            $distances[] = [
                'route_label' => $route->route_short_name,
                'length' => $length,
            ];
        }

        uasort($distances, function($a, $b) {
            return $a['length'] <=> $b['length'];
        });

        return view('fun-stats', compact('averageSpeedsDesc', 'averageSpeedsAsc', 'bussesCount', 'tramsCount', 'distances'));
    }

    public function getRoutesFromStation(Request $request)
    {
        $trip = Trip::query()->whereHas('stops', function ($query) use ($request) { $query->where('stop_name', $request->query('stop_name')); })->get();

        $routes = $trip->map(function ($trip) {
            return Route::query()->where('route_id', $trip->route_id)->first();
        })->unique('route_short_name');

        return response()->json($routes);
    }
}

