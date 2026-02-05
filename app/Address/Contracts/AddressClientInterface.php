<?php

declare(strict_types=1);

namespace App\Address\Contracts;

use App\Address\DTO\AddressResult;
use App\Address\DTO\CityResult;
use App\Address\DTO\StreetResult;

interface AddressClientInterface
{
    /**
     * @return array<CityResult>
     */
    public function searchCities(string $query): array;

    /**
     * @return array<StreetResult>
     */
    public function searchStreets(string $query, string $municipality): array;

    /**
     * @return array<AddressResult>
     */
    public function searchAddresses(string $query, string $street, string $municipality): array;
}
