<?php

namespace SpeckShipping\Service;

use SpeckShipping\Entity\ShippingClass;
use SpeckCart\Entity\Cart;
use SpeckCart\Entity\CartItem;

interface ShippingInterface
{
    public function getShippingClass(CartItem $item);

    public function getShippingClasses(Cart $cart);

    public function getShippingClassCost(ShippingClass $sc);

    public function getShippingCost(Cart $cart);
}
