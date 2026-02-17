<?php

declare(strict_types=1);

namespace App\Cart\Riesenia;

use Litipk\BigNumbers\Decimal;
use Riesenia\Cart\Cart;
use Riesenia\Cart\PromotionInterface;

class CartRieseniePromoCodePromotion implements PromotionInterface
{
    public function isEligible(Cart $cart): bool
    {

        return ! array_any($cart->getItems(), fn ($item) => $item instanceof DiscountCartItem);
    }

    public function beforeApply(Cart $cart): void {}

    public function afterApply(Cart $cart): void {}

    public function apply(Cart $cart): void
    {
        $discount = $cart->getContext()->getData()['discount'];

        $subtotal = Decimal::fromInteger(0);
        foreach ($cart->getItems() as $item) {
            $subtotal = $subtotal->add(
                Decimal::fromFloat($item->getUnitPrice())->mul(Decimal::fromFloat($item->getCartQuantity()))
            );
        }

        $discountPrice = -$subtotal->mul(Decimal::fromFloat($discount / 100))->asFloat();

        $cart->addItem(new DiscountCartItem($discountPrice, $discount.'%'));
    }
}
