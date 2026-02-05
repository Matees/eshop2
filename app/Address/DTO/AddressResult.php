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
