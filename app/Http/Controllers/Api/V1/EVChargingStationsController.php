<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\Api\V1\NearbyRequest;
use App\Services\Api\V1\Google\GoogleMapService;
use Illuminate\Http\Request;
use App\DTOs\NearbySearchDTO;

class EVChargingStationsController extends BaseApiController
{
    public function __construct(protected GoogleMapService $googleMapService)
    {
        $this->googleMapService = $googleMapService;
    }

    public function nearby(NearbyRequest $request, NearbySearchDTO $nearbySearchDTO)
    {
        $nearbySearchDTO->lat = $request->lat;
        $nearbySearchDTO->lng = $request->lng;
        $nearbySearchDTO->radius = 10500;
        $nearbySearchDTO->type = 'charging station';
        $nearbySearchDTO->keyword = 'Electric Car Vehicle Charging station';

        $nearbySearches = $this->googleMapService->nearbySearch($nearbySearchDTO);

        return $this->handleGoogleApiResponse($nearbySearches);
    }

    public function getDirections(Request $request)
    {

    }
}
