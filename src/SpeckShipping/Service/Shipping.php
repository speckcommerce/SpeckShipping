<?php

namespace SpeckShipping\Service;

use Zend\EventManager\EventManagerAwareInterface;
use Zend\EventManager\EventManagerAwareTrait;
use SpeckCart\Entity\Cart;
use SpeckCart\Entity\CartItem;
use SpeckShipping\Entity\ShippingClass;
use SpeckCatalogCart\CatalogProductMeta;

class Shipping implements ShippingInterface, EventManagerAwareInterface
{
    use EventManagerAwareTrait;

    protected $entityMapper;

    public function getShippingClass(CartItem $item)
    {
        $response = $this->getEventManager()->trigger(
            __FUNCTION__, $this, array('cart_item' => $item)
        );
        $sc = $response->last();
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

    public function getShippingCost(Cart $cart, $decimalPlaces=null)
    {
        $shippingClasses = $this->getShippingClasses($cart);
        $cost = (object) array('value' => 0);

        $this->getEventManager()->trigger(
            __FUNCTION__, $this, array(
                'shipping_classes' => $shippingClasses,
                'cost'             => $cost,
            )
        );

        if ($decimalPlaces) {
            return $this->ceilingDecimal($cost->value, $decimalPlaces);
        }
        return $cost->value;
    }

    protected function ceilingDecimal($price, $decimalPlaces)
    {
        if (!is_int($decimalPlaces) || $decimalPlaces > 0) {
            throw new \Exception('decimal places must be an integer above zero');
        }
        $mult = pow(10, $decimalPlaces);
        return ceil($price * $mult) / $mult;
    }
}
