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
        $options = $data->options;
        $shippingClasses = $data->shipping_classes;

        $highest = $data->cost;
        foreach ($shippingClasses as $sc) {
            if ($sc->getCost() > $highest) {
                $highest = $sc->getCost();
            }
        }
        $data->cost = $highest;
    }

    //this is for shipping classes that came from
    //CatalogProductResolver
    public function quantityCostIncrementer($e)
    {
        $data    = $e->getParam('data');
        $shippingClasses = $data->shipping_classes;

        $list = array();
        foreach ($shippingClasses as $sc) {
            if ($sc->get('resolved') !== $this->productResolverClass) {
                continue;
            }
            if (!is_array($sc->get('sc_cart_cost_modifiers'))) {
                continue;
            }
            if (!is_array($sc->get('sc_cart_cost_modifiers')['qty_increment'])) {
                continue;
            }

            if (!isset($list[$sc->get('product_id')]))
            {
                $item = array(
                    'qty' => 0,
                    'classes' => array(),
                );
                $item['qty_increment_cost'] = $sc->get('qty_increment_cost')
                   ?: $sc->get('sc_cart_cost_modifiers')['qty_increment']['default_inc'];

                $list[$sc->get('product_id')] = $item;
            }
            $list[$sc->get('product_id')]['qty'] += $sc->get('quantity');
            $list[$sc->get('product_id')]['classes'][] = $sc;
        }

        foreach ($list as $pid => $info) {
            $cost = $sc->getCost() + ($info['qty_increment_cost'] * ($info['qty'] -1));
            foreach ($info['classes'] as $sc) {
                $sc->setCost($cost);
            }
        }
    }

    public function shippingPriority($e)
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
