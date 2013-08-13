<?php

namespace SpeckShipping\Entity;

use SpeckCart\Entity\CartItemInterface;

class ShippingClass implements ShippingClassInterface
{
    protected $classId;
    protected $name;
    protected $baseCost;
    protected $meta = array();

    //note: these are not hydrated/extracted with the hydrator
    protected $cost; //calculated cost after custom logic (if any)
    protected $cartItem;

    public function get($key)
    {
        if (isset($this->meta[$key])) {
            return $this->meta[$key];
        }
        return null;
    }

    public function set($key, $val)
    {
        $this->meta[$key] = $val;
    }

    public function getClassId()
    {
        return $this->classId;
    }

    public function setClassId($classId)
    {
        $this->classId = $classId;
        return $this;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    public function getMeta()
    {
        return $this->meta;
    }

    public function setMeta(array $meta = null)
    {
        if (null === $meta) {
            $meta = array();
        }
        $this->meta = $meta;
        return $this;
    }

    public function getBaseCost()
    {
        return $this->baseCost;
    }

    public function setBaseCost($baseCost)
    {
        $this->baseCost = $baseCost;
        return $this;
    }

    public function getCartItem()
    {
        return $this->cartItem;
    }

    public function setCartItem(CartItemInterface $cartItem)
    {
        $this->cartItem = $cartItem;
        return $this;
    }

    public function getCost()
    {
        return $this->cost;
    }

    public function setCost($cost)
    {
        $this->cost = $cost;
        return $this;
    }
}
