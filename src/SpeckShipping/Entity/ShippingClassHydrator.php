<?php

namespace SpeckShipping\Entity;

use Zend\Stdlib\Hydrator\HydratorInterface;

class ShippingClassHydrator implements HydratorInterface
{
    public function hydrate(array $data, $model)
    {
        $model->setClassId($data['shipping_class_id'])
            ->setName($data['name'])
            ->setBaseCost($data['base_cost'])
            ->setMeta(json_decode($data['meta'], true));

        foreach(array('site_meta', 'product_meta', 'category_meta') as $key) {
            if (array_key_exists($key, $data)) {
                $this->addLinkerMeta($model, $data[$key], $key);
            }
        }

        return $model;
    }

    public function addLinkerMeta($model, $jsonMeta, $name)
    {
        $metaArray = json_decode($jsonMeta, true);

        if ($name === 'product_meta') {
            return $model->setProductMeta($metaArray);
        } elseif ($name === 'category_meta') {
            return $model->setCategoryMeta($metaArray);
        } elseif ($name === 'website_meta') {
            return $model->setWebsiteMeta($metaArray);
        }
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
