<?php

namespace App\Services\Api\V1\Google;

use Google\Client;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use App\DTOs\NearbySearchDTO;

class GoogleMapService
{
    protected $geoCodingService;

    protected $directionService;

    protected $cacheDuration;

    private $key;

    public function __construct(protected Client $client)
    {
        $this->client = $client;
        $this->cacheDuration = config('google.cache_duration');
        $this->key = config('google.api_key');
    }

    /**
     * Search for places by text search using new Place API
     *
     * @param array $requestData
     * @param string $fields
     * @return array
     */
    public function textSearchNew($requestData = [], array|string $fields = '*')
    {
        $response = Http::withHeaders([
            'X-Goog-Api-Key' => $this->key,
            'X-Goog-FieldMask' => $fields,
        ])
        ->post('https://places.googleapis.com/v1/places:searchText', $requestData);

        if ($response->successful()) {
            return $this->handleResponse($response->json());
        }

        if ($response->clientError()) {
            return $this->handleResponse($response->json());
        }

        if ($response->serverError()) {
            return $this->handleResponse(['status' => 'SERVER_ERROR']);
        }
    }

    /**
     * Search for places nearby a location using Place API (old API)
     *
     * @param \App\DTOs\NearbySearchDTO $nearbySearchDTO
     * @return array
     */
    public function nearbySearch(NearbySearchDTO $nearbySearchDTO)
    {
        $location = $nearbySearchDTO->lat . ',' . $nearbySearchDTO->lng;
        $cacheKey = 'nearby_search_' . md5($location);

        return Cache::remember($cacheKey, $this->cacheDuration, function () use ($nearbySearchDTO, $location) {
            $response = Http::get('https://maps.googleapis.com/maps/api/place/nearbysearch/json', [
                'location' => $location,
                'radius' => $nearbySearchDTO->radius, // Default radius in meters
                'type' => $nearbySearchDTO->type,
                'keyword' => $nearbySearchDTO->keyword,
                'key' => config('google.api_key'),
            ]);

            if ($response->successful()) {
                return $this->handleResponse($response->json());
            }

            if ($response->clientError()) {
                return $this->handleResponse($response->json());
            }

            if ($response->serverError()) {
                return $this->handleResponse(['status' => 'SERVER_ERROR']);
            }
       });
    }

    /**
     * Get geocode information for an address.
     *
     * @param string $address
     * @return array
     */
    public function geocode($address)
    {
        $cacheKey = 'geocode_' . md5($address);

        return Cache::remember($cacheKey, $this->cacheDuration, function () use ($address) {
            $response = Http::get('https://maps.googleapis.com/maps/api/geocode/json', [
                'address' => $address,
                'key' => config('google.api_key')
            ]);

            if ($response->successful()) {
                return $this->handleResponse($response->json());
            }

            if ($response->clientError()) {
                return $this->handleResponse($response->json());
            }

            if ($response->serverError()) {
                return $this->handleResponse(['status' => 'SERVER_ERROR']);
            }
        });
    }

    /**
     * Get directions between two locations.
     *
     * @param string $origin
     * @param string $destination
     * @return array
     */
    public function getDirections($origin, $destination)
    {
        $cacheKey = 'directions_' . md5($origin . '_' . $destination);

        return Cache::remember($cacheKey, $this->cacheDuration, function () use ($origin, $destination) {

            $response = Http::get('https://maps.googleapis.com/maps/api/directions/json', [
                'origin' => $origin,
                'destination' => $destination,
                'key' => config('google.api_key')
            ]);

            if ($response->successful()) {

                return $this->handleResponse($response->json());
            }

            if ($response->clientError()) {
                return $this->handleResponse($response->json());
            }

            if ($response->serverError()) {
                return $this->handleResponse(['status' => 'SERVER_ERROR']);
            }
        });
    }

    /**
     * Handle Google API response.
     *
     * @param array $data
     * @return array
     */
    protected function handleResponse($data)
    {
        if (isset($data['status']) && $data['status'] != 'OK') {
            return [
                'error' => $data['status'],
                'error_message' => $data['error_message'] ?? 'An error occurred'
            ];
        } else if (isset($data['error']) && $data['error']['status']) {
            return [
                'error' => $data['error']['status'],
                'error_message' => $data['error']['message'] ?? 'An error occurred'
            ];
        }

        if (isset($data['results'])) {
            return $data['results'];
        }

        return $data;
    }
}
