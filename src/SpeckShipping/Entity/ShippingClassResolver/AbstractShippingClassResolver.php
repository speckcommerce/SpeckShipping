<?php

namespace SpeckShipping\Entity\ShippingClassResolver;

use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
use SpeckCart\Entity\CartItemInterface;

abstract class AbstractShippingClassResolver
    implements ShippingClassResolverInterface, ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    protected $cartItem;

    public function getCartItem()
    {
        return $this->cartItem;
    }

    public function setCartItem(CartItemInterface $cartItem)
    {
        $this->cartItem = $cartItem;
        return $this;
    }
}
