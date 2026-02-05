<?php

declare(strict_types=1);

namespace App\Address\DTO;

final readonly class StreetResult
{
    public function __construct(
        public string $name,
    ) {}

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
