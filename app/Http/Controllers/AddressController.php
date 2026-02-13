<?php

namespace App\Http\Controllers;

use App\Address\Contracts\AddressClientInterface;
use App\Http\Requests\SearchAddressesRequest;
use App\Http\Requests\SearchCitiesRequest;
use App\Http\Requests\SearchStreetsRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class AddressController extends Controller
{
    private const int PLACES_QUERY_TTL = 3600;

    public function __construct(
        private readonly AddressClientInterface $addressClient,
    ) {}

    public function cities(SearchCitiesRequest $request): JsonResponse
    {
        /** @var string $query */
        $query = $request->validated('q');

        $cacheKey = 'cities_'.md5($query);

        $cities = Cache::remember($cacheKey, self::PLACES_QUERY_TTL, function () use ($query) {
            return $this->addressClient->searchCities($query);
        });

        return response()->json($cities);
    }

    public function streets(SearchStreetsRequest $request): JsonResponse
    {
        /** @var string $query */
        $query = $request->validated('q');
        /** @var string $municipality */
        $municipality = $request->validated('municipality');

        $cacheKey = 'streets_'.md5($query.'_'.$municipality);

        $streets = Cache::remember($cacheKey, self::PLACES_QUERY_TTL, function () use ($query, $municipality) {
            return $this->addressClient->searchStreets($query, $municipality);
        });

        return response()->json($streets);
    }

    public function addresses(SearchAddressesRequest $request): JsonResponse
    {
        /** @var string $query */
        $query = $request->validated('q');
        /** @var string $street */
        $street = $request->validated('street');
        /** @var string $municipality */
        $municipality = $request->validated('municipality');

        $cacheKey = 'addresses_'.md5($query.'_'.$street.'_'.$municipality);

        /** @var array<int, array<string, ?string>> $addresses */
        $addresses = Cache::remember($cacheKey, self::PLACES_QUERY_TTL, function () use ($query, $street, $municipality) {
            return $this->addressClient->searchAddresses($query, $street, $municipality);
        });

        return response()->json($addresses);
    }
}
