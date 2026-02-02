<?php

namespace App\Casts;

use App\Models\Address;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

readonly class AddressCast implements CastsAttributes
{
    public function get($model, $key, $value, $attributes): Address
    {
        return new Address(
            $attributes['address_line_one'],
            $attributes['address_line_two'],
            $attributes['address_line_three']
        );
    }

    /**
     * @return array<string, string>
     */
    public function set($model, $key, $value, $attributes): array
    {
        if (! $value instanceof Address) {
            throw new \InvalidArgumentException('Value must be an Address instance.');
        }

        return [
            'address_line_one' => $value->lineOne,
            'address_line_two' => $value->lineTwo,
            'address_line_three' => $value->lineThree,
        ];
    }
}
