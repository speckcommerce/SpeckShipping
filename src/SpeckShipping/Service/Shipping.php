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
use Zend\Stdlib\Parameters;


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
        $sc = $this->getEventManager()->trigger(
            __FUNCTION__, $this, array('cart_item' => $item)
        )->last();

        $sc->setCartItem($item);
        $this->setShippingClassCost($sc);

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

    public function setShippingClassCost(ShippingClassInterface $sc)
    {
        $sc->setCost($sc->getBaseCost());

        $this->getEventManager()->trigger(
            __FUNCTION__, $this, array('shipping_class' => $sc)
        );
    }

    public function getShippingCost(CartInterface $cart, array $options = array(), $returnData = false)
    {
        $shippingClasses = $this->getShippingClasses($cart);

        $data = (object) array(
            'cart'             => $cart,
            'options'          => $options,
            'shipping_classes' => $shippingClasses,
        );

        $response = $this->getEventManager()->trigger(
            __FUNCTION__, $this, array('data' => $data)
        );

        if ($returnData) {
            return $data;
        }

        return $response->last();
    }

    public function getShippingClassById($id)
    {
        return $this->getMapper('sc')->getShippingClassById($id);
    }

    public function persistShippingClass(ShippingClassInterface $sc)
    {
        return $this->getMapper('sc')->persist($sc);
    }

    public function linkShippingClass($shippingClassId, $type, $typeId, array $meta = array())
    {
        if (!in_array($type, array('product', 'category', 'website'))) {
            throw new \Exception('invalid type!');
        }

        return $this->getMapper($type)
            ->linkShippingClass($shippingClassId, $typeId, $meta);
    }
}
