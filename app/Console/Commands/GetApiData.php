<?php

namespace App\Console\Commands;

use App\Models\Vehicle;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class GetApiData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'get-api-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Gets data from the api';


    public function handle()
    {
        Log::warning('I have been called upon.');
        try {
            $apiData = json_decode(file_get_contents('https://gps.sctpiasi.ro/json'), true);
        } catch (\Exception $e) {
            Log::warning($e);
        }

        if (isset($apiData)) {
            foreach ($apiData as $vehicleData) {
                /** @var Vehicle $vehicle */
                $vehicle = Vehicle::firstOrCreate(['vehicle_name' => $vehicleData['vehicleName']]);

                $vehicleDate = Carbon::parse($vehicleData['vehicleDate']);

                $vehicle->update([
                    'vehicle_lat' => strlen($vehicleData['vehicleLat']) ? $vehicleData['vehicleLat'] : 0,
                    'vehicle_long' => strlen($vehicleData['vehicleLong']) ? $vehicleData['vehicleLong'] : 0,
                    'vehicle_date' => $vehicleDate
                ]);
            }
        }
    }
}
