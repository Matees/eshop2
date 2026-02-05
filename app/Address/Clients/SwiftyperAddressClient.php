<?php

declare(strict_types=1);

namespace App\Address\Clients;

use App\Address\Contracts\AddressClientInterface;
use App\Address\DTO\AddressResult;
use App\Address\DTO\CityResult;
use App\Address\DTO\StreetResult;
use Illuminate\Support\Facades\Http;

final class SwiftyperAddressClient implements AddressClientInterface
{
    private const string API_URL = 'https://api.swiftyper.sk/v1/places/query';

    private const string DEFAULT_COUNTRY = 'SK';

    public function __construct(
        private readonly string $apiKey,
    ) {}

    /**
     * @return array<CityResult>
     */
    public function searchCities(string $query): array
    {
        $data = $this->fetchData([
            'query' => $query,
            'country' => [self::DEFAULT_COUNTRY],
        ]);

        return collect($data)
            ->filter(fn (array $item): bool => isset($item['municipality']))
            ->map(fn (array $item): CityResult => new CityResult(
                name: $item['municipality'],
                placeId: $item['place_id'],
                postalCode: $item['postal_code'] ?? null,
                lat: $item['latlng']['lat'] ?? null,
                lon: $item['latlng']['lng'] ?? null,
            ))
            ->unique('name')
            ->values()
            ->all();
    }

    /**
     * @return array<StreetResult>
     */
    public function searchStreets(string $query, string $municipality): array
    {
        $data = $this->fetchData([
            'query' => $query,
            'municipality' => $municipality,
            'country' => [self::DEFAULT_COUNTRY],
        ]);

        return collect($data)
            ->filter(fn (array $item): bool => isset($item['street']))
            ->map(fn (array $item): StreetResult => new StreetResult(
                name: $item['street'],
            ))
            ->unique('name')
            ->values()
            ->all();
    }

    /**
     * @return array<AddressResult>
     */
    public function searchAddresses(string $query, string $street, string $municipality): array
    {
        $searchQuery = $query !== '' ? "$street $query" : $street;

        $data = $this->fetchData([
            'query' => $searchQuery,
            'municipality' => $municipality,
            'country' => [self::DEFAULT_COUNTRY],
            'limit' => 15,
        ]);

        return collect($data)
            ->filter(fn (array $item): bool => $this->hasValidStreetNumber($item))
            ->map(fn (array $item): AddressResult => new AddressResult(
                streetNumber: $this->formatStreetNumber($item),
                postalCode: $item['postal_code'] ?? null,
                placeId: $item['place_id'] ?? null,
            ))
            ->unique('streetNumber')
            ->values()
            ->all();
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return array<int, array<string, mixed>>
     */
    private function fetchData(array $payload): array
    {
        $response = Http::withHeaders([
            'X-Swiftyper-API-Key' => $this->apiKey,
        ])->post(self::API_URL, $payload);

        if (! $response->successful()) {
            return [];
        }

        return $response->json();
    }

    /**
     * @param  array<string, mixed>  $item
     */
    private function hasValidStreetNumber(array $item): bool
    {
        return isset($item['street'])
            && (isset($item['street_number']) || isset($item['building_number']));
    }

    /**
     * @param  array<string, mixed>  $item
     */
    private function formatStreetNumber(array $item): string
    {
        $streetNumber = $item['street_number'] ?? null;
        $buildingNumber = $item['building_number'] ?? null;

        if ($streetNumber !== null && $buildingNumber !== null) {
            return "$streetNumber/$buildingNumber";
        }

        return $buildingNumber ?? $streetNumber ?? '';
    }
}
