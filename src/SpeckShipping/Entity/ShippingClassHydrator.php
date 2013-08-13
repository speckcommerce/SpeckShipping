<?php

namespace SpeckShipping\Entity;

use Zend\Stdlib\Hydrator\HydratorInterface;

class ShippingClassHydrator implements HydratorInterface
{
    public function hydrate(array $data, $model)
    {
        return $model->setClassId($data['shipping_class_id'])
            ->setName($data['name'])
            ->setBaseCost($data['base_cost'])
            ->setMeta(json_decode($data['meta'], true));
    }

    public function extract($model)
    {
        return array(
            'shipping_class_id' => $model->getClassId(),
            'name'              => $model->getName(),
            'base_cost'         => $model->getBaseCost(),
            'meta'              => json_encode($model->getMeta()),
        );
    }
}
