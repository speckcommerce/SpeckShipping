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
        //todo: remove these test lines
            $sc = new \SpeckShipping\Entity\ShippingClass;
            $sc->setBaseCost(9.99);
            $sc->set('cost_modifiers', array(
                array(
                    'name' => 'incremental_qty',
                    'options' => array(
                        'quantity' => 2,
                        'cost'     => .35,
                    ),
                ),
            ));
            $item->setQuantity(4);
        //end of test

        $sc->setCartItem($item);
        $this->getShippingClassCost($sc);

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
        $shippingClasses = $this->getShippingClasses($cart);
        $cost = (object) array('value' => 0);

        $this->getEventManager()->trigger(
            __FUNCTION__, $this, array(
                'shipping_classes' => $shippingClasses,
                'cost'             => $cost,
            )
        );

        return $cost->value;
    }
}
