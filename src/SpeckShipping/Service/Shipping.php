<?php

namespace SpeckShipping\Service;

use Zend\EventManager\EventManagerAwareInterface;
use Zend\EventManager\EventManagerAwareTrait;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
use SpeckCart\Entity\CartInterface;
use SpeckCart\Entity\CartItemInterface;
use SpeckShipping\Entity\ShippingClassInterface;
use SpeckCatalogCart\CatalogProductMeta;


class Shipping implements ShippingInterface, EventManagerAwareInterface,
    ServiceLocatorAwareInterface
{
    use EventManagerAwareTrait;
    use ServiceLocatorAwareTrait;

    protected $mappers = array(
        'sc'       => 'speckshipping_sc_mapper',
        'product'  => 'speckshipping_p_sc_mapper',
        'category' => 'speckshipping_c_sc_mapper',
        'website'  => 'speckshipping_w_sc_mapper',
    );

    public function getMapper($name)
    {
        if (!array_key_exists($name, $this->mappers)) {
            throw new \Exception('invalid mapper name');
        }
        if (is_string($this->mappers[$name])) {
            $this->mappers[$name] = $this->getServiceLocator()->get($this->mappers[$name]);
        }
        return $this->mappers[$name];
    }

    public function getShippingClass(CartItemInterface $item)
    {
        $response = $this->getEventManager()->trigger(
            __FUNCTION__, $this, array('cart_item' => $item)
        );
        $sc = $response->last();
        $sc->setCartItem($item);
        $this->getShippingClassCost($sc);

        return $sc;
    }

    public function getShippingClasses(CartInterface $cart)
    {
        $classes = array();
        foreach ($cart->getItems() as $item) {
            $classes[] = $this->getShippingClass($item);
        }
        return $classes;
    }

    public function getShippingClassCost(ShippingClassInterface $sc)
    {
        $sc->setCost($sc->getBaseCost());

        $this->getEventManager()->trigger(
            __FUNCTION__, $this, array('shipping_class' => $sc)
        );
    }

    public function getShippingCost(CartInterface $cart, $decimalPlaces=null)
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

    public function getShippingClassById($id)
    {
        return $this->getMapper('sc')->getShippingClassById($id);
    }

    protected function ceilingDecimal($price, $decimalPlaces)
    {
        if (!is_int($decimalPlaces) || !$decimalPlaces > 0) {
            throw new \Exception('decimal places must be an integer above zero');
        }
        $mult = pow(10, $decimalPlaces);
        return ceil($price * $mult) / $mult;
    }

    public function persistShippingClass(ShippingClassInterface $sc)
    {
        $mapper = $this->getMapper('sc');
        $mapper->persistShippingClass($sc);
    }

    public function linkShippingClass(ShippingClassInterface $sc, $type, $typeId)
    {
        $mapper = $this->getMapper($type);
        $mapper->linkShippingClass($sc, $typeId);
    }
}
