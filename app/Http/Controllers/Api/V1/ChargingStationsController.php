<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\Api\V1\NearbyRequest;
use App\Services\Api\V1\Google\GoogleMapService;
use Illuminate\Http\Request;
use App\DTOs\NearbySearchDTO;
use App\Http\Requests\Api\V1\GetDirectionsRequest;
use App\Models\ChargingStation;

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

    public function nearby(NearbyRequest $request, NearbySearchDTO $nearbySearchDTO)
    {
        $nearbySearchDTO->lat = $request->lat;
        $nearbySearchDTO->lng = $request->lng;
        $nearbySearchDTO->radius = 100500;
        $nearbySearchDTO->type = 'charging_station';
        $nearbySearchDTO->keyword = 'Electric Car Vehicle Charging station in CEBU';

        $nearbySearches = $this->googleMapService->nearbySearch($nearbySearchDTO);

        return $this->handleGoogleApiResponse($nearbySearches);
    }

    public function getDirections(GetDirectionsRequest $request)
    {
        $directions = $this->googleMapService->getDirections($request->origin, $request->destination);

        return $this->handleGoogleApiResponse($directions);
    }
}
