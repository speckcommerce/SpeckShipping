<?php

namespace SpeckShipping\Event;

use SpeckShipping\Entity\CostModifier\CostModifierInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;

class ShippingClassEvents
{
    use ServiceLocatorAwareTrait;

    public function getModuleConfig()
    {
        return $this->getServiceLocator()->get('speckshipping_config');
    }

    public function shippingClassCostModifiers($e)
    {
        $sc = $e->getParam('shipping_class');
        $costMods = $sc->get('sc_cost_modifiers');
        if (null === $costMods) {
            return;
        }

        $config = $this->getModuleConfig();

        foreach ($costMods as $name => $options) {
            if (!array_key_exists($name, $config['sc_cost_modifiers'])) {
                return;
            }
            $cm = new $config['sc_cost_modifiers'][$name];
            $cm->setOptions(is_array($options) ? $options : array());
            $cm->setShippingClass($sc);
            $cm->setServiceLocator($this->getServiceLocator());
            $cm->adjustCost();
        }

        return;
    }

    public function shippingClassForCartItem($e)
    {
        $item     = $e->getParam('cart_item');
        $metaFqcn = get_class($item->getMetadata());
        $config   = $this->getModuleConfig();
        if (!array_key_exists($metaFqcn, $config['shipping_class_resolvers'])) {
            return;
        }
        $sl = $this->getServiceLocator();
        $resolver = $sl->get($config['shipping_class_resolvers'][$metaFqcn]);
        $resolver->setServiceLocator($this->getServiceLocator());
        $resolver->setCartItem($item);

        return $resolver->resolveShippingClass();
    }
}
