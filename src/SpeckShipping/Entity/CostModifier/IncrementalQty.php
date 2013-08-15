<?php

namespace SpeckShipping\Entity\CostModifier;

class IncrementalQty extends AbstractCostModifier
{
    protected $quantity = 1; //threshhold qty
    protected $cost     = 0.00; //cost associated per qty above the threshhold


    //todo : probably need to remove this, as we need to check all items in the cart for similar product Ids

    public function adjustCost()
    {
        $sc      = $this->getShippingClass();
        $itemQty = $sc->getCartItem()->getQuantity();
        $minQty  = $this->getQuantity();

        if ($itemQty > $minQty) {
            $increments = $itemQty - $minQty;
            $cost = (float) $sc->getCost() + ($this->getCost() * $increments);
            $sc->setCost($cost);
        }
        return;
    }

    public function getQuantity()
    {
        return $this->quantity;
    }

    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;
        return $this;
    }

    public function getCost()
    {
        return $this->cost;
    }

    public function setCost($cost)
    {
        $this->cost = $cost;
        return $this;
    }
}
