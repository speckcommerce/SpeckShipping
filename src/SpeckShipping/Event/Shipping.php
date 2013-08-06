<?php

namespace SpeckShipping\Event;


class Shipping
{
    public function cartItemAdd($e, $sl)
    {
    }

    public function cartItemPersist($e, $sl)
    {
    }

    public function userInfoComplete($e, $sl)
    {
    }

    //default logic for shipping cost
    public function cartShippingCost($e, $sl)
    {
    }

    public function incrementalQuantityCost($e)
    {
        $sc = $e->getParams('shippingClass');
        if (null === $sc->get('incremental_quantity_cost')) {
            return;
        }
    }


}
