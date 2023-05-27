<?php

namespace App;

use App\Models\Route;
use App\Models\Shape;
use App\Models\Stop;
use App\Models\Trip;
use App\Models\Vehicle;
use Illuminate\Support\Facades\Http;

class OpenData
{
    private string $apiKey = 'ISSGAxXKjfa8J7LDMYOhj9fEPuZKZeRO5Q69qpW0';

    public array $options = [];

    public function __construct()
    {
        $this->options = [
            'X-API-KEY' => $this->apiKey,
            'X-Agency-Id' => 1
        ];
    }

    public function getVehicles()
    {
        $response = Http::withHeaders($this->options)->get('https://api.tranzy.dev/v1/opendata/vehicles')->body();

        return json_decode($response, true);
    }

    /**
     * @param string|null $routeId
     * @return \Illuminate\Database\Eloquent\Collection
     */

    public function getLocalVehicles(string $routeId = null, string $tripId = null)
    {
        if ($routeId && $tripId) {
            return Vehicle::query()->latest()->where('route_id', $routeId)->where('trip_id', $tripId)->take(100)->get()->unique('label');
        }

        return Vehicle::query()->latest()->take(100)->get()->unique('label');
    }

    public function getRoutes()
    {
        $routes = Route::all();

        if ($routes->count()) {
            return $routes;
        }

        $response = Http::withHeaders($this->options)->get('https://api.tranzy.dev/v1/opendata/routes')->body();

        foreach (json_decode($response, true) as $route) {
            (new Route())->fill($route)->save();
        }


        return Route::all();
    }

    public function getRouteByName($routeName)
    {
        return Route::query()->where('route_short_name', $routeName)->get()->first();
    }
    public function getStops()
    {
        $stops = Stop::all();

        if ($stops->count()) {
            return $stops;
        }

        $response = Http::withHeaders($this->options)->get('https://api.tranzy.dev/v1/opendata/stops')->body();

        foreach (json_decode($response, true) as $stop) {
            (new Stop())->fill($stop)->save();
        }

        return Stop::all();
    }

    public function getShapes()
    {
        $shapes = Shape::all();

        if ($shapes->count()) {
            return $shapes;
        }

        $response = Http::withHeaders($this->options)->get('https://api.tranzy.dev/v1/opendata/shapes')->body();

        foreach (json_decode($response, true) as $shape) {
            (new Shape())->fill($shape)->save();
        }

        return Shape::all();
    }

    public function getShape($shapeId)
    {
        $shape = Shape::query()->where('shape_id', $shapeId)->get();

        if ($shape) {
            return $shape;
        }

        $shapes = $this->getShapes();

        return $shapes->where('shape_id', $shapeId)->first();
    }

    public function getTrip(string $routeId, int $direction)
    {
        $trip = Trip::query()->where('route_id', $routeId)->where('direction_id', $direction)->first();

        if ($trip) {
            return $trip;
        }

        $response = Http::withHeaders($this->options)->get("https://api.tranzy.dev/v1/opendata/trips")->body();

        $trips = collect(json_decode($response, true));

        foreach ($trips as $trip) {
            (new Trip())->fill($trip)->save();
        }

        return Trip::query()->where('route_id', $routeId)->where('direction_id', $direction)->first();;
    }

    public function getStopTrip()
    {
        ini_set('max_execution_time', '1240000');

        $response = Http::withHeaders($this->options)->get("https://api.tranzy.dev/v1/opendata/stop_times")->body();

        $stopTrips = collect(json_decode($response, true));

        foreach ($stopTrips as $stopTrip) {
            /** @var Trip $trip */
            $trip = Trip::query()->where('trip_id', $stopTrip['trip_id'])->first();

            /** @var Stop $stop */
            $stop = Stop::query()->where('stop_id', $stopTrip['stop_id'])->first();
            $sequence = $stopTrip['stop_sequence'];

            if (!$trip || !$stop) {
                continue;
            }

            $trip->stops()->attach($stop, ['sequence' => $sequence]);
        }
    }
}
