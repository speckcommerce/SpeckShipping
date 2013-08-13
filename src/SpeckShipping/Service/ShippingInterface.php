<?php

namespace SpeckShipping\Service;

use SpeckShipping\Entity\ShippingClassInterface;
use SpeckCart\Entity\CartInterface;
use SpeckCart\Entity\CartItemInterface;

interface ShippingInterface
{
    public function getShippingClassById($id);

    public function getShippingClass(CartItemInterface $item);

    public function getShippingClasses(CartInterface $cart);

    public function getShippingClassCost(ShippingClassInterface $sc);

    public function getShippingCost(CartInterface $cart, $decimalPlaces = null);

    public function persistShippingClass(ShippingClassInterface $sc);

    public function linkShippingClass(ShippingClassInterface $sc, $type, $typeId);
}
