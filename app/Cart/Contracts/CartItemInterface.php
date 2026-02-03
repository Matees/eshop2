<?php

declare(strict_types=1);

namespace App\Cart\Contracts;

interface CartItemInterface
{
    public function getId(): string;

    public function getName(): string;

    public function setQuantity(float $quantity): void;

    public function getQuantity(): float;

    public function getUnitPrice(): float;

    public function getTaxRate(): float;
}
