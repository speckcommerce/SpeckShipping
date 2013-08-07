<?php

namespace SpeckShipping\Entity\CostModifier;

use Zend\ServiceManager\ServiceLocatorInterface;
use SpeckShipping\Entity\ShippingClass;

interface CostModifierInterface
{
    public function setServiceLocator(ServiceLocatorInterface $sl);

    public function getServiceLocator();

    public function adjustCost();

    public function setOptions(array $options = array());

    public function getShippingClass();

    public function setShippingClass(ShippingClass $sc);
}
