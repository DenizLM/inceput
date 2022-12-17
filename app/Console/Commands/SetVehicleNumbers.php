<?php

namespace App\Console\Commands;

use App\Models\Vehicle;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SetVehicleNumbers extends Command
{
    const VEHICLES_NUMBER = [
        '1054' => 'CS',
        '2000' => '47',
        '1942' => '47',
        '1954' => '47',
        '1946' => '47'
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

      foreach ($vehicles as $vehicle) {
          if (!isset(self::VEHICLES_NUMBER[$vehicle->vehicle_name])) {
              continue;
          }
          $vehicle->vehicle_number = self::VEHICLES_NUMBER[$vehicle->vehicle_name];
          $vehicle->save();
      }
    }
}
