<?php

namespace App\Http\Controllers;

use App\Address\Contracts\AddressClientInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class AddressController extends Controller
{
    private const int PLACES_QUERY_TTL = 3600;

    public function __construct(
        private readonly AddressClientInterface $addressClient,
    ) {}

    public function cities(Request $request): JsonResponse
    {
        /** @var string $query */
        $query = $request->get('q', '');

        $cacheKey = 'cities_'.md5($query);

        /** @var array<int, array<string, mixed>> $cities */
        $cities = Cache::remember($cacheKey, self::PLACES_QUERY_TTL, function () use ($query) {
            return array_map(
                fn ($city) => $city->toArray(),
                $this->addressClient->searchCities($query)
            );
        });

        return response()->json($cities);
    }

    public function streets(Request $request): JsonResponse
    {
        /** @var string $query */
        $query = $request->get('q', '');
        /** @var string $municipality */
        $municipality = $request->get('municipality', '');

        if ($municipality === '') {
            return response()->json([]);
        }

        $cacheKey = 'streets_'.md5($query.'_'.$municipality);

        /** @var array<int, array<string, string>> $streets */
        $streets = Cache::remember($cacheKey, self::PLACES_QUERY_TTL, function () use ($query, $municipality) {
            return array_map(
                fn ($street) => $street->toArray(),
                $this->addressClient->searchStreets($query, $municipality)
            );
        });

        return response()->json($streets);
    }

    public function addresses(Request $request): JsonResponse
    {
        /** @var string $query */
        $query = $request->get('q', '');
        /** @var string $street */
        $street = $request->get('street', '');
        /** @var string $municipality */
        $municipality = $request->get('municipality', '');

        if (strlen($municipality) < 2 || strlen($street) < 1) {
            return response()->json([]);
        }

        $cacheKey = 'addresses_'.md5($query.'_'.$street.'_'.$municipality);

        /** @var array<int, array<string, ?string>> $addresses */
        $addresses = Cache::remember($cacheKey, self::PLACES_QUERY_TTL, function () use ($query, $street, $municipality) {
            return array_map(
                fn ($address) => $address->toArray(),
                $this->addressClient->searchAddresses($query, $street, $municipality)
            );
        });

        return response()->json($addresses);
    }
}
