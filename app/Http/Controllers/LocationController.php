<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    private $baseUrl;
    private $apiKey;

    public function __construct()
    {
        $this->baseUrl = config('services.csc.base_url');
        $this->apiKey = config('services.csc.key');
    }

    // MAIN METHOD – handles all types of requests
   public function getLocationData(Request $request)
{
    $countryIso = $request->countryIso;
    $stateIso   = $request->stateIso;

    // Cache key
    $cacheKey = "location_{$countryIso}_{$stateIso}";

    // Check if cache already exists (before retrieving)
    $isCached = Cache::has($cacheKey);

    // Get or store the data
    $data = Cache::remember($cacheKey, now()->addHours(24), function () use ($countryIso, $stateIso) {

        if (!$countryIso && !$stateIso) {
            return $this->fetchCountries();
        }

        if ($countryIso && !$stateIso) {
            return $this->fetchStates($countryIso);
        }

        return $this->fetchCities($countryIso, $stateIso);
    });

    // Final response with cache flag
    return response()->json([
        'cached' => $isCached,  // true = returned from cache, false = fresh API request
        'data'   => $data,
    ]);
}


    // Fetch all countries
    private function fetchCountries()
    {
        return Http::withHeaders([
            'X-CSCAPI-KEY' => $this->apiKey
        ])->get("{$this->baseUrl}/countries")->json();
    }

    // Fetch states of a specific country
    private function fetchStates($countryIso)
    {
        return Http::withHeaders([
            'X-CSCAPI-KEY' => $this->apiKey
        ])->get("{$this->baseUrl}/countries/{$countryIso}/states")->json();
    }

    // Fetch cities of a state
    private function fetchCities($countryIso, $stateIso)
    {
        return Http::withHeaders([
            'X-CSCAPI-KEY' => $this->apiKey
        ])->get("{$this->baseUrl}/countries/{$countryIso}/states/{$stateIso}/cities")->json();
    }
}
