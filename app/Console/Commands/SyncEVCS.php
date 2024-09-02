<?php

namespace App\Console\Commands;

use App\Models\ChargingStation;
use App\Services\Api\V1\Google\GoogleMapService;
use Illuminate\Console\Command;

class SyncEVCS extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:evcs {--keyword=Electric Vehicle Charging Station Philippines} {--fields=places.id,places.displayName,places.location,places.formattedAddress,places.evChargeOptions,places.businessStatus}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync Electric Vehicle Charging Stations from Google Map';

    /**
     * Execute the console command.
     */
    public function handle(GoogleMapService $googleMapService)
    {
        if ($this->confirm('Do you wish to continue?')) {
            $requestData = ['textQuery' => $this->option('keyword')];
            $fields = explode(',', $this->option('fields'));
            $places = $googleMapService->textSearchNew($requestData, $fields)['places'];

            if (isset($places['error'])) {
                $this->error($places['error_message']);
            }

            foreach ($places as $place) {
                ChargingStation::updateOrCreate(
                    ['place_id' => $place['id']],
                    [
                        'name' => $place['displayName'],
                        'location' => $place['location'],
                        'connector_types' => isset($place['evChargeOptions']) && ! empty($place['evChargeOptions']) ? $place['evChargeOptions']['connectorAggregation'] : [],
                        'business_status' => $place['businessStatus'],
                        'address' => $place['formattedAddress'],
                    ]
                );
            }
        }

        $this->info('The syncing was successful!');
    }
}
