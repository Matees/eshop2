<?php

namespace App\Casts;

use App\Models\Address;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

/** @implements CastsAttributes<Address, Address> */
readonly class AddressCast implements CastsAttributes
{
    public function get(Model $model, string $key, $value, $attributes): Address
    {
        return new Address(
            $attributes['address_line_one'],
            $attributes['address_line_two'],
            $attributes['address_line_three']
        );
    }

    /**
     * @param  ?Address  $value
     * @return array<string, string>
     */
    public function set(Model $model, string $key, $value, $attributes): array
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
