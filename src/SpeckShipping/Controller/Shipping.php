<?php

namespace SpeckShipping\Controller;

use Zend\Mvc\Controller\AbstractActionController;

class Shipping extends AbstractActionController
{
    protected $services = array(
        'shipping' => 'speckshipping_shipping_service',
        'catalog_cart' => 'catalog_cart_service',
    );

    public function getService($name)
    {
        if (!array_key_exists($name, $this->services)) {
            throw new \Exception('invalid service name');
        }
        if (is_string($this->services[$name])) {
            $this->services[$name] = $this->getServiceLocator()->get($this->services[$name]);
        }
        return $this->services[$name];
    }

    public function indexAction()
    {
        $cartId = $this->params('cartId');
        $ss = $this->getService('shipping');
        $cs = $this->getService('catalog_cart');
        $cart = $cs->getSessionCart();

        $cost = $ss->getShippingCost($cart);
        var_dump($cost); die();
    }
}
