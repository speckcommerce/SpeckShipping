<?php

namespace SpeckShipping\Event;

use Zend\EventManager\EventInterface;

class CartShippingCost
{
    protected $shippingClasses;
    protected $cost = 0;

    public function __construct(EventInterface $e)
    {
        $data = $e->getParam('data');
        $this->setShippingClasses($data->shipping_classes);
    }

    //default logic for shipping cost
    //get most expensive shipping class
    public function getShippingCost()
    {
        foreach ($this->shippingClasses as $sc) {
            if ($sc->getCost() > $this->cost) {
                $this->cost = $sc->getCost();
            }
        }
        return $this->getCost();
    }

    public function getShippingClasses()
    {
        return $this->shippingClasses;
    }

    public function setShippingClasses($shippingClasses)
    {
        $this->shippingClasses = $shippingClasses;
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
