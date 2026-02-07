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
     * @param  array<string, mixed>  $data
     */
    public static function fromApiResponse(array $data): self
    {
        return new self(
            name: $data['municipality'],
            placeId: $data['place_id'],
            postalCode: $data['postal_code'] ?? null,
            lat: $data['latlng']['lat'] ?? null,
            lon: $data['latlng']['lng'] ?? null,
        );
    }

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
