<?php

declare(strict_types=1);

namespace App\Address\DTO;

final readonly class CityResult
{
    public function __construct(
        public string $name,
        public string $placeId,
        public ?string $postalCode,
        public ?float $lat,
        public ?float $lon,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'placeId' => $this->placeId,
            'postalCode' => $this->postalCode,
            'lat' => $this->lat,
            'lon' => $this->lon,
        ];
    }
}
