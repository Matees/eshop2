<?php

namespace App\Models;

readonly class Address
{
    public function __construct(
        public string $lineOne,
        public string $lineTwo,
        public string $lineThree,
    ) {}
}
