<?php

namespace SpeckShipping\Service;

use Zend\EventManager\EventManagerAwareInterface;
use Zend\EventManager\EventManagerAwareTrait;
use SpeckCart\Entity\Cart;
use SpeckCart\Entity\CartItem;
use SpeckShipping\Entity\ShippingClass;

class Shipping implements ShippingInterface, EventManagerAwareInterface
{
    use EventManagerAwareTrait;

    protected $entityMapper;

    public function getShippingClass(CartItem $item)
    {
        //todo: remove this;
        $sc = new \SpeckShipping\Entity\ShippingClass;
        $sc->setCost($this->getShippingClassCost($sc));

        return $sc;
    }

    public function getShippingClasses(Cart $cart)
    {
        $classes = array();
        foreach ($cart->getItems() as $item) {
            $classes[] = $this->getShippingClass($item);
        }
        return $classes;
    }

    public function getShippingClassCost(ShippingClass $sc)
    {
        $sc->setCost($sc->getBaseCost());

        $this->getEventManager()->trigger(
            __FUNCTION__, $this, array('shipping_class' => $sc)
        );
    }

    public function getShippingCost(Cart $cart)
    {
        $cost = 0;
        $shippingClasses = $this->getShippingClasses($cart);

        $this->getEventManager()->trigger(
            __FUNCTION__, $this, array('shipping_classes' => $shippingClasses)
        );

        return $cost;
    }
}
