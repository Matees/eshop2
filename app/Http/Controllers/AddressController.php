<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class AddressController extends Controller
{
    private const SWIFTTYPER_MUNICIPALITY_URL = 'https://api.swiftyper.sk/v1/places/municipality';

    private const SWIFTTYPER_QUERY_URL = 'https://api.swiftyper.sk/v1/places/query';

    public function cities(Request $request): JsonResponse
    {
        $query = $request->get('q', '');

        if (strlen($query) < 2) {
            return response()->json([]);
        }

        $cacheKey = 'cities_'.md5($query);

        $cities = Cache::remember($cacheKey, 0, function () use ($query) {
            $response = Http::withHeaders([
                'X-Swiftyper-API-Key' => config('services.swiftyper.api_key'),
            ])->post(self::SWIFTTYPER_MUNICIPALITY_URL, [
                'query' => $query,
                'country' => ['SK'],
            ]);

            if (! $response->successful()) {
                return [];
            }

            $results = collect($response->json())
                ->filter(fn ($item) => isset($item['municipality']))
                ->map(function ($item) {
                    $name = $item['municipality'] ?? null;

                    return [
                        'name' => $name,
                        'placeId' => $item['place_id'],
                        'lat' => $item['latlng']['lat'],
                        'lon' => $item['latlng']['lng'],
                    ];
                })
                ->filter(fn ($item) => $item['name'] !== null)
                ->unique('name')
                ->values();

            return $results;
        });

        return response()->json($cities);
    }

    public function streets(Request $request): JsonResponse
    {
        $query = $request->get('q', '');
        $municipality = $request->get('municipality', '');

        if (strlen($query) < 2 || strlen($municipality) < 2) {
            return response()->json([]);
        }

        $cacheKey = 'streets_'.md5($query.'_'.$municipality);

        $streets = Cache::remember($cacheKey, 0, function () use ($query, $municipality) {
            $response = Http::withHeaders([
                'X-Swiftyper-API-Key' => config('services.swiftyper.api_key'),
            ])->post(self::SWIFTTYPER_QUERY_URL, [
                'query' => $query,
                'municipality' => $municipality,
                'country' => ['SK'],
            ]);

            if (! $response->successful()) {
                return [];
            }

            return collect($response->json())
                ->filter(fn ($item) => isset($item['street']))
                ->map(fn ($item) => ['name' => $item['street']])
                ->unique('name')
                ->values();
        });

        return response()->json($streets);
    }

    public function postcode(Request $request): JsonResponse
    {
        $city = $request->get('city', '');
        $street = $request->get('street', '');

        if (strlen($city) < 2) {
            return response()->json(['postcode' => null]);
        }

        $cacheKey = 'postcode_'.md5($city.'_'.$street);

        $postcode = Cache::remember($cacheKey, 3600, function () use ($city, $street) {
            $searchQuery = $street ? "$street, $city, Slovakia" : "$city, Slovakia";

            $response = Http::withHeaders([
                'User-Agent' => 'Eshop/1.0',
            ])->get(self::NOMINATIM_URL, [
                'q' => $searchQuery,
                'format' => 'json',
                'addressdetails' => 1,
                'countrycodes' => 'sk',
                'limit' => 1,
            ]);

            if (! $response->successful()) {
                return null;
            }

            $data = $response->json();

            if (empty($data)) {
                return null;
            }

            return $data[0]['address']['postcode'] ?? null;
        });

        return response()->json(['postcode' => $postcode]);
    }
}
