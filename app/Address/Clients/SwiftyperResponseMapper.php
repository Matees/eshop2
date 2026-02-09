<?php

declare(strict_types=1);

namespace App\Address\Clients;

use App\Address\DTO\AddressResult;
use App\Address\DTO\CityResult;
use App\Address\DTO\StreetResult;

final class SwiftyperResponseMapper
{
    /**
     * @param  array<int, array<string, mixed>>  $data
     * @return array<CityResult>
     */
    public function mapCities(array $data): array
    {
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
     * @param  array<int, array<string, mixed>>  $data
     * @return array<StreetResult>
     */
    public function mapStreets(array $data): array
    {
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
     * @param  array<int, array<string, mixed>>  $data
     * @return array<AddressResult>
     */
    public function mapAddresses(array $data): array
    {
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
     * @param  array<string, mixed>  $data
     */
    private function hasValidStreetNumber(array $data): bool
    {
        return isset($data['street'])
            && (isset($data['street_number']) || isset($data['building_number']));
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function formatStreetNumber(array $data): string
    {
        $streetNumber = $data['street_number'] ?? null;
        $buildingNumber = $data['building_number'] ?? null;

        if ($streetNumber !== null && $buildingNumber !== null) {
            return "$streetNumber/$buildingNumber";
        }

        return $buildingNumber ?? $streetNumber ?? '';
    }
}
