<?php

namespace SpeckShipping\Event;

use Zend\EventManager\EventInterface;
use SpeckCatalogCart\Model\CartProductMeta;

class ShippingPriority
{
    public function __construct(EventInterface $e)
    {
    }

    public function adjustCosts()
    {
    }

    public function getCommonShippingProrities(array $shippingClasses)
    {
        if (count($shippingClasses()) < 1) {
            throw new \Exception('no shipping classes set');
        }

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
