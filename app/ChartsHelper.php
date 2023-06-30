<?php

namespace App;

use App\Models\Route;
use App\Models\Vehicle;
use Illuminate\Support\Facades\DB;

class ChartsHelper
{
    public function __construct()
    {
    }

    public static function getAverageSpeedAllRoutes(int $interval = 1, $order = 'desc')
    {
        $routes = Route::query()->get();

        $average = Vehicle::query()
            ->select(DB::raw('AVG(speed) as average, route_id'))
            ->where('speed', '!=', '0')
            ->where('created_at', '>', now()->subHours($interval))
            ->groupBy('route_id')
            ->orderBy('average', $order)
            ->get();

        return $average->map(function ($item) use ($routes) {
            return [$routes->firstWhere('route_id', $item->route_id)?->route_short_name => $item->average];
        })->filter(function ($item) {
            $key = array_keys($item);

            if ($key[0] === '' || $key[0] > 100) {
                return false;
            }

            return true;
        })->mapWithKeys(fn($item) => [ array_keys($item)[0] => $item[array_keys($item)[0]]])->toArray();
    }
}
