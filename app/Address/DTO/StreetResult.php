<?php

declare(strict_types=1);

namespace App\Address\DTO;

final readonly class StreetResult
{
    public function __construct(
        public string $name,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromApiResponse(array $data): self
    {
        return new self(
            name: $data['street'],
        );
    }

    /**
     * @return array<string, string>
     */
    public function toArray(): array
    {
        return [
            'name' => $this->name,
        ];
    }
}
