<?php

namespace SpeckShipping\Service;

use SpeckShipping\Entity\ShippingClassInterface;
use SpeckCart\Entity\CartInterface;
use SpeckCart\Entity\CartItemInterface;
use Zend\Stdlib\AbstractOptions;

interface ShippingInterface
{
    public function getShippingClassById($id);

    public function getShippingClass(CartItemInterface $item);

    public function getShippingClasses(CartInterface $cart);

    public function setShippingClassCost(ShippingClassInterface $sc);

    public function getShippingCost(CartInterface $cart, array $options = array());

    public function persistShippingClass(ShippingClassInterface $sc);

    public function linkShippingClass($shippingClassId, $type, $typeId, array $meta = array());
}
