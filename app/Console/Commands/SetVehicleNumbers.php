<?php

namespace App\Console\Commands;

use App\Models\Vehicle;
use App\Models\VehicleLog;
use Carbon\Carbon;
use GeometryLibrary\MathUtil;
use GoogleMaps\GoogleMaps;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Log;

class SetVehicleNumbers extends Command
{
    const ROUTES = [
        7 => [
            'origin' => '47.172874,27.538521',
            'destination' => '47.143410, 27.642338'
        ]
    ];

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'setvehicles:numbers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Gets data from the api';


    public function handle()
    {
        $vehicles = Vehicle::all();

        foreach (self::ROUTES as $key => $route) {
            $polyRoutes[$key] = (new GoogleMaps)->load('directions')
                ->setParam([
                    'origin' => $route['origin'],
                    'destination' => $route['destination'],
                    'mode' => 'transit',
                    'transit_routing_preference' => 'less_walking',
                    'departure_time' => now()->subHour(5)->timestamp
                ])->get();
            $polyRoutes[$key] = json_decode($polyRoutes[$key], true)['routes'][0]['overview_polyline']['points'];
        }

        foreach ($vehicles as $vehicle) {
            $matchingRoutes = [];
            foreach (self::ROUTES as $key => $route) {
                $isOnRoute = true;
//                $logs = VehicleLog::where('vehicle_name', $vehicle->vehicle_name)->get();
//
//                if ($logs->count()) {
//                    foreach ($logs as $log) {
//                        $isOnRoute = self::isLocationOnEdgeOrPath(
//                            ['lat' => $log->lat, 'lng' => $log->long],
//                            $polyRoutes[$key],
//                            false,true,60
//                        );
//                    }
//                    if (!$isOnRoute) {
//                        continue;
//                    }
//                }

                $isOnRoute = self::isLocationOnEdgeOrPath(
                    ['lat' => round($vehicle->vehicle_lat, 7), 'lng' => round($vehicle->vehicle_long, 7)],
                    $polyRoutes[$key],
                    false,true,2
                );

                VehicleLog::firstOrCreate([
                    'vehicle_name' => $vehicle->vehicle_name,
                    'lat' => $vehicle->vehicle_lat,
                    'long' => $vehicle->vehicle_long
                ]);

                if ($isOnRoute) {
                    $matchingRoutes[] = $key;
                }
            }
            if (count($matchingRoutes) === 1) {
                $vehicle->vehicle_number = $matchingRoutes[0];
            } else {
                $vehicle->vehicle_number = 'undefined';
            }
            $vehicle->save();
        }
    }


    private static function isLocationOnEdgeOrPath($point, $poly, $closed, $geodesic, $toleranceEarth) {

        $size = count( $poly );

        if ($size == 0) {
            return false;
        }

        $tolerance = $toleranceEarth / MathUtil::$earth_radius;
        $havTolerance = MathUtil::hav($tolerance);
        $lat3 = deg2rad($point['lat']);
        $lng3 = deg2rad($point['lng']);
        $prev = !empty($closed) ? $poly[$size - 1] : $poly[0];
        $lat1 = deg2rad($prev['lat']);
        $lng1 = deg2rad($prev['lng']);

        if ($geodesic) {
            foreach($poly as $val) {
                $lat2 = deg2rad(round($val['lat'], 6));
                $lng2 = deg2rad(round($val['lng'], 6));
                if ( self::isOnSegmentGC($lat1, $lng1, $lat2, $lng2, $lat3, $lng3, $havTolerance)) {
                    return true;
                }
                $lat1 = $lat2;
                $lng1 = $lng2;
            }
        } else {
            // We project the points to mercator space, where the Rhumb segment is a straight line,
            // and compute the geodesic distance between point3 and the closest point on the
            // segment. This method is an approximation, because it uses "closest" in mercator
            // space which is not "closest" on the sphere -- but the error is small because
            // "tolerance" is small.
            $minAcceptable = $lat3 - $tolerance;
            $maxAcceptable = $lat3 + $tolerance;
            $y1 = MathUtil::mercator($lat1);
            $y3 = MathUtil::mercator($lat3);
            $xTry = [];
            foreach($poly as $val) {
                $lat2 = deg2rad(round($val['lat'], 6));
                $y2 = MathUtil::mercator($lat2);
                $lng2 = deg2rad(round($val['lng'], 6));
                if (max($lat1, $lat2) >= $minAcceptable && min($lat1, $lat2) <= $maxAcceptable) {
                    // We offset longitudes by -lng1; the implicit x1 is 0.
                    $x2 = MathUtil::wrap($lng2 - $lng1, -M_PI, M_PI);
                    $x3Base = MathUtil::wrap($lng3 - $lng1, -M_PI, M_PI);
                    $xTry[0] = $x3Base;
                    // Also explore wrapping of x3Base around the world in both directions.
                    $xTry[1] = $x3Base + 2 * M_PI;
                    $xTry[2] = $x3Base - 2 * M_PI;

                    foreach($xTry as $x3) {
                        $dy = $y2 - $y1;
                        $len2 = $x2 * $x2 + $dy * $dy;
                        $t = $len2 <= 0 ? 0 : MathUtil::clamp(($x3 * $x2 + ($y3 - $y1) * $dy) / $len2, 0, 1);
                        $xClosest = $t * $x2;
                        $yClosest = $y1 + $t * $dy;
                        $latClosest = MathUtil::inverseMercator($yClosest);
                        $havDist = MathUtil::havDistance($lat3, $latClosest, $x3 - $xClosest);
                        if ($havDist < $havTolerance) {
                            return true;
                        }
                    }
                }
                $lat1 = $lat2;
                $lng1 = $lng2;
                $y1 = $y2;
            }
        }
        return false;
    }

    private static function isOnSegmentGC( $lat1, $lng1, $lat2, $lng2, $lat3, $lng3, $havTolerance) {

        $havDist13 = MathUtil::havDistance($lat1, $lat3, $lng1 - $lng3);
        if ($havDist13 <= $havTolerance) {
            return true;
        }
        $havDist23 = MathUtil::havDistance($lat2, $lat3, $lng2 - $lng3);
        if ($havDist23 <= $havTolerance) {
            return true;
        }
        $sinBearing = self::sinDeltaBearing($lat1, $lng1, $lat2, $lng2, $lat3, $lng3);
        $sinDist13 = MathUtil::sinFromHav($havDist13);
        $havCrossTrack = MathUtil::havFromSin($sinDist13 * $sinBearing);
        if ($havCrossTrack > $havTolerance) {
            return false;
        }
        $havDist12 = MathUtil::havDistance($lat1, $lat2, $lng1 - $lng2);
        $term = $havDist12 + $havCrossTrack * (1 - 2 * $havDist12);
        if ($havDist13 > $term || $havDist23 > $term) {
            return false;
        }
        if ($havDist12 < 0.74) {
            return true;
        }
        $cosCrossTrack = 1 - 2 * $havCrossTrack;
        $havAlongTrack13 = ($havDist13 - $havCrossTrack) / $cosCrossTrack;
        $havAlongTrack23 = ($havDist23 - $havCrossTrack) / $cosCrossTrack;
        $sinSumAlongTrack = MathUtil::sinSumFromHav($havAlongTrack13, $havAlongTrack23);
        return $sinSumAlongTrack > 0;  // Compare with half-circle == PI using sign of sin().
    }

    private static function sinDeltaBearing( $lat1, $lng1, $lat2, $lng2, $lat3, $lng3) {

        $sinLat1 = sin($lat1);
        $cosLat2 = cos($lat2);
        $cosLat3 = cos($lat3);
        $lat31 = $lat3 - $lat1;
        $lng31 = $lng3 - $lng1;
        $lat21 = $lat2 - $lat1;
        $lng21 = $lng2 - $lng1;
        $a = sin($lng31) * $cosLat3;
        $c = sin($lng21) * $cosLat2;
        $b = sin($lat31) + 2 * $sinLat1 * $cosLat3 * MathUtil::hav($lng31);
        $d = sin($lat21) + 2 * $sinLat1 * $cosLat2 * MathUtil::hav($lng21);
        $denom = ($a * $a + $b * $b) * ($c * $c + $d * $d);
        return $denom <= 0 ? 1 : ($a * $d - $b * $c) / sqrt($denom);
    }
}
