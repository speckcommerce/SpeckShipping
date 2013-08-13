<?php

namespace SpeckShipping\Entity;

use SpeckCart\Entity\CartItemInterface;

interface ShippingClassInterface
{
    public function setClassId($classId);

    public function getClassId();

    public function setBaseCost($baseCost);

    public function getBaseCost();

    public function setName($name);

    public function getName();

    public function setMeta(array $meta = array());

    public function getMeta();

    public function set($k, $v);

    public function get($k);

    public function setCartItem(CartItemInterface $cartItem);

    public function getCartItem();

    public function setCost($cost);

    public function getCost();
}
