<?php

namespace SpeckShipping\Entity;

class ShippingClassHydrator
{
    public function hydrate(array $data, $model)
    {
        if (!$model instanceOf ShippingClass) {
            $this->notShippingClassException($model);
        }

        return $model->setClassId($data['shipping_class_id'])
            ->setName($data['name'])
            ->setBaseCost($data['base_cost'])
            ->setMeta(json_decode($data['meta'], true));
    }

    public function notShippingClassException($m)
    {
        //todo: "expected instance of shipping class, got: class or type "
        throw new \Exception("expected instance of shippingclass");
    }

    public function extract($model)
    {
        if (!$model instanceOf ShippingClass) {
            $this->notShippingClassException($model);
        }

        return array(
            'shipping_class_id' => $model->getClassId(),
            'name'              => $model->getName(),
            'base_cost'         => $model->getBaseCost(),
            'meta'              => json_encode($model->getMeta()),
        );
    }
}
