<?php

namespace SpeckShipping\Entity\CostModifier;

use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
use SpeckShipping\Entity\ShippingClassInterface;

abstract class AbstractCostModifier implements CostModifierInterface,
    ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    protected $shippingClass;

    public function setOptions(array $options = array())
    {
        foreach ($options as $k => $v) {
            $set = 'set' . ucfirst($k);
            $this->$set($v);
        }
    }

    public function adjustCost()
    {
        //replace this in your extending class
    }

    public function getShippingClass()
    {
        return $this->shippingClass;
    }

    public function setShippingClass(ShippingClassInterface $shippingClass)
    {
        $this->shippingClass = $shippingClass;
        return $this;
    }
}
