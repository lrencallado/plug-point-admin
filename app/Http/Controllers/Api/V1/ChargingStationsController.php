<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\Api\V1\NearbyRequest;
use App\Services\Api\V1\Google\GoogleMapService;
use Illuminate\Http\Request;
use App\Http\Requests\Api\V1\GetDirectionsRequest;
use App\Models\ChargingStation;
use App\Services\Api\V1\LocationPointsService;

class ChargingStationsController extends BaseApiController
{
    public function __construct(
        protected GoogleMapService $googleMapService,
        protected ChargingStation $chargingStation,
        )
    {
        $this->googleMapService = $googleMapService;
        $this->chargingStation = $chargingStation;
    }

    public function index(Request $request)
    {
        return $this->resource($this->chargingStation->all());
    }

    public function nearby(NearbyRequest $request, LocationPointsService $locationPointsService, ChargingStation $chargingStation)
    {
        $locationWithinRadius = [];
        $otherStations = [];

        /**
         * Loop through the list of coordinations and check if each coordinate
         * is within the specified radius from the center point.
         */
        foreach ($chargingStation->all() as $station) {
            $distance = $locationPointsService->haversineDistance($request->latitude, $request->longitude, $station->location['latitude'], $station->location['longitude']);

            if ($distance <= $request->radius) {
                $locationWithinRadius[] = $station;
            } else {
                $otherStations[] = $station;
            }
        }

        return $this->resource(['nearby' => $locationWithinRadius, 'other' => $otherStations]);
    }

    public function getDirections(GetDirectionsRequest $request)
    {
        $directions = $this->googleMapService->getDirections($request->origin, $request->destination);

        return $this->handleGoogleApiResponse($directions);
    }
}
