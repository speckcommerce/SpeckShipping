<?php

namespace SpeckShipping\Entity\ShippingClassResolver;

use Zend\ServiceManager\ServiceLocatorInterface;
use SpeckCart\Entity\CartItemInterface;

interface ShippingClassResolverInterface
{
    public function setCartItem(CartItemInterface $item);

    public function getCartItem();

    public function setServiceLocator(ServiceLocatorInterface $sl);

    public function getServiceLocator();

    public function resolveShippingClass();
}
