<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class AddressController extends Controller
{
    private const SWIFTTYPER_QUERY_URL = 'https://api.swiftyper.sk/v1/places/query';

    public function cities(Request $request): JsonResponse
    {
        $query = $request->get('q', '');

        $cacheKey = 'cities_'.md5($query);

        $cities = Cache::remember($cacheKey, 0, function () use ($query) {
            $response = Http::withHeaders([
                'X-Swiftyper-API-Key' => config('services.swiftyper.api_key'),
            ])->post(self::SWIFTTYPER_QUERY_URL, [
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
                        'postalCode' => $item['postal_code'],
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

        if ($municipality === '') {
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

    public function addresses(Request $request): JsonResponse
    {
        $query = $request->get('q', '');
        $street = $request->get('street', '');
        $municipality = $request->get('municipality', '');

        if (strlen($municipality) < 2 || strlen($street) < 1) {
            return response()->json([]);
        }

        $searchQuery = $query ? "$street $query" : $street;
        $cacheKey = 'addresses_'.md5($searchQuery.'_'.$municipality);

        $addresses = Cache::remember($cacheKey, 0, function () use ($searchQuery, $municipality) {
            $response = Http::withHeaders([
                'X-Swiftyper-API-Key' => config('services.swiftyper.api_key'),
            ])->post(self::SWIFTTYPER_QUERY_URL, [
                'query' => $searchQuery,
                'municipality' => $municipality,
                'country' => ['SK'],
                'limit' => 15,
            ]);

            if (! $response->successful()) {
                return [];
            }

            return collect($response->json())
                ->filter(fn ($item) => isset($item['street']) && (isset($item['street_number']) || isset($item['building_number'])))
                ->map(function ($item) {
                    $number = $item['building_number'] ?? $item['street_number'] ?? '';
                    if (isset($item['building_number']) && isset($item['street_number'])) {
                        $number = $item['street_number'].'/'.$item['building_number'];
                    }

                    return [
                        'streetNumber' => $number,
                        'postalCode' => $item['postal_code'] ?? null,
                        'placeId' => $item['place_id'] ?? null,
                    ];
                })
                ->unique('streetNumber')
                ->values();
        });

        return response()->json($addresses);
    }
}
