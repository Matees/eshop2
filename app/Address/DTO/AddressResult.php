<?php

declare(strict_types=1);

namespace App\Address\DTO;

final readonly class AddressResult
{
    public function __construct(
        public string $streetNumber,
        public ?string $postalCode,
        public ?string $placeId,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromApiResponse(array $data): self
    {
        return new self(
            streetNumber: self::formatStreetNumber($data),
            postalCode: $data['postal_code'] ?? null,
            placeId: $data['place_id'] ?? null,
        );
    }

    /**
     * @param  array<string, mixed>  $data
     */
    public static function hasValidStreetNumber(array $data): bool
    {
        return isset($data['street'])
            && (isset($data['street_number']) || isset($data['building_number']));
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private static function formatStreetNumber(array $data): string
    {
        $streetNumber = $data['street_number'] ?? null;
        $buildingNumber = $data['building_number'] ?? null;

        if ($streetNumber !== null && $buildingNumber !== null) {
            return "$streetNumber/$buildingNumber";
        }

        return $buildingNumber ?? $streetNumber ?? '';
    }

    /**
     * @return array<string, ?string>
     */
    public function toArray(): array
    {
        return [
            'streetNumber' => $this->streetNumber,
            'postalCode' => $this->postalCode,
            'placeId' => $this->placeId,
        ];
    }
}
