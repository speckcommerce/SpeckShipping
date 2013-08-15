<?php

namespace SpeckShipping\Event;

use SpeckCatalogCart\Model\CartProductMeta;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;

class CartShippingCost implements ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    protected $productResolverClass =
        'SpeckShipping\Entity\ShippingClassResolver\CatalogProductResolver';

    //default logic for shipping cost
    //get most expensive shipping class
    public function cartShippingCost($e)
    {
        $data    = $e->getParam('data');
        $options = $data['options'];
        $shippingClasses = $data['shipping_classes'];

        $highest = $options->cost;
        foreach ($data['shipping_classes'] as $sc) {
            if ($sc->getCost() > $highest) {
                $highest = $sc->getCost();
            }
        }
        $options->cost = $highest;
    }

    //this is for shipping classes that came from
    //CatalogProductResolver
    public function quantityCostIncrementer($e)
    {
        $data    = $e->getParam('data');
        $options = $data['options'];
        $shippingClasses = $data['shipping_classes'];

        $list = array();
        foreach ($shippingClasses as $sc) {
            if ($sc->get('resolved') !== $this->productResolverClass) continue;
            if (!is_array($sc->get('qty_increment'))) continue;

            $list[$sc->get('product_id')]['qty'] += $sc->get('quantity');
            $list[$sc->get('product_id')]['classes'][] = $sc;
        }

        var_dump($list); die();
        foreach ($list as $pid => $stuff) {
        }
    }






    //option handlers
    protected function decimal($e, $params)
    {

        $this->ceilingDecimal($cost, $places);

    }



    protected function ceilingDecimal($number, $decimalPlaces)
    {
        if (!is_int($decimalPlaces) || !$decimalPlaces > 0) {
            throw new \Exception('decimal places must be an integer above zero');
        }
        $mult = pow(10, $decimalPlaces);
        return ceil($number * $mult) / $mult;
    }

    public function shippingPriority($e, $params)
    {
    }

    public function getCommonShippingProrities(array $shippingClasses)
    {
        if (count($shippingClasses()) < 1) {
            throw new \Exception('no shipping classes set');
        }

        $this->shippingClasses = $shippingClasses;

        //build array of shipping class names, associated to the shipping class
        $scPriorities = array();
        foreach ($shippingClasses as $sc) {
            $scPriorities[$sc->getClassId()] = array_keys($sc->get('shipping_priorities'));
        }

        //merge all priority names
        $names = array();
        foreach($scPriorities as $scId => $priorityNames) {
            $names = array_merge($priorityNames, $names);
        }

        //prune names down to the common ones for all shipping classes
        foreach($scPriorities as $scId => $pNames) {
            $names = array_intersect($names, $pNames);
        }

        return $names;
    }
}
