<?php

namespace SpeckShipping\Event;

class IncrementalQtyCost
{
    protected $productResolverClass =
        'SpeckShipping\Entity\ShippingClassResolver\CatalogProductResolver';

    protected $groupedShippingClasses      = array();
    protected $productQuantities           = array();
    protected $productIncrementCosts       = array();
    protected $initialProductShippingCosts = array();

    public function __construct($e)
    {
        $data = $e->getParam('data');

        foreach ($data->shipping_classes as $sc) {
            $this->prepShippingClass($sc);
        }
    }

    public function adjustCosts()
    {
        foreach($this->groupedShippingClasses as $pid => $classes) {
            $cost = $this->getFullCost($pid);
            foreach ($classes as $sc) {
                $sc->setCost($cost);
            }
        }
    }

    public function prepShippingClass($sc)
    {
        if (
            $sc->get('resolved') !== $this->productResolverClass
            || !is_array($sc->get('sc_cart_cost_modifiers'))
            || !is_array($sc->get('sc_cart_cost_modifiers')['qty_increment'])
        ) {
            return;
        }

        $productId = $sc->get('product_id');

        $this->setInitialShippingCost($productId, $sc->getCost());

        $this->groupedShippingClasses[$productId][] = $sc;

        $incrementCost = $sc->get('qty_increment_cost')
            ?: $sc->get('sc_cart_cost_modifiers')['qty_increment']['default_inc'];
        $this->setIncrementalCost($productId, $incrementCost);

        $this->addProductQuantity($productId, $sc->get('quantity'));
    }

    public function setIncrementalCost($productId, $cost)
    {
        if (!isset($this->productIncrementCosts[$productId])) {
            $this->productIncrementCosts[$productId] = 0;
        }
        $this->productIncrementCosts[$productId] = $cost;
        return $this;
    }

    public function getFullCost($productId)
    {
        $incrementCost = $this->productIncrementCosts[$productId];
        $initialCost   = $this->getInitialShippingCost($productId);
        $quantity      = $this->getProductQuantity($productId);

        return $initialCost + ($incrementCost * ($quantity - 1));
    }

    public function addProductQuantity($productId, $quantity)
    {
        if (!isset($this->productQuantities[$productId])) {
            $this->productQuantities[$productId] = 0;
        }
        $this->productQuantities[$productId] += (int) $quantity;
        return $this;
    }

    public function getProductQuantity($productId)
    {
        return $this->productQuantities[$productId];
    }

    public function setInitialShippingCost($productId, $cost)
    {
        $this->initialProductShippingCosts[$productId] = $cost;
        return $this;
    }

    public function getInitialShippingCost($productId)
    {
        return $this->initialProductShippingCosts[$productId];
    }
}
