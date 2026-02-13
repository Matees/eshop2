<?php

declare(strict_types=1);

namespace App\Cart\Contracts;

interface CartItemInterface
{
    public string $id { get; }

    public string $name { get; }

    public float $quantity { get; set; }

    public float $unitPrice { get; }

    public float $taxRate { get; }

    public float $totalPrice { get; }
}
