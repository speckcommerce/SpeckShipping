<?php

namespace SpeckShipping\View\Helper;

use Zend\View\Helper\HelperInterface;
use Zend\View\Helper\AbstractHelper;
use SpeckCart\Entity\CartInterface;

class Shipping extends AbstractHelper
{
    protected $shippingService;

    public function __invoke()
    {
        return $this;
    }

    public function cartCost(CartInterface $cart, $decimal=2)
    {
        return $this->getShippingService()->getShippingCost($cart, $decimal);
    }

    public function getShippingService()
    {
        return $this->shippingService;
    }

    public function setShippingService($shippingService)
    {
        $this->shippingService = $shippingService;
        return $this;
    }
}
