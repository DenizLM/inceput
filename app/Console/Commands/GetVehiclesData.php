<?php

namespace App\Console\Commands;

use App\Models\Vehicle;
use App\OpenData;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class GetVehiclesData extends Command
{
    public function __construct(protected OpenData $openData) {
        parent::__construct();
    }

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'open-data:get-vehicles-data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Gets vehicle data from OpenData';


    public function handle()
    {
        $this->info('Getting vehicle data...');
        $openDataVehicles = $this->openData->getVehicles();

        $this->mapOpenDataVehiclesToVehicles($openDataVehicles);

        $this->info('Done!');
    }

    public function mapOpenDataVehiclesToVehicles(array $openDataVehicles)
    {
        array_walk($openDataVehicles, function (&$vehicle, $key) {
            unset($vehicle['id']);
            $vehicle['created_at'] = Carbon::now();
        });

       Vehicle::query()->insert($openDataVehicles);
    }
}
