<?php

namespace SpeckShipping\Mapper;

use Zend\Stdlib\Hydrator\ArraySerializable as Hydrator;
use Zend\Stdlib\ArrayObject;

class Product extends AbstractMapper
{
    protected $tableName = 'shipping_class_product';

    public function __construct()
    {
        parent::__construct();
        $this->hydrator        = $this->hydrators['array'];
        $this->entityPrototype = $this->entityPrototypes['array'];
    }

    public function linkShippingClass($shippingClassId, $productId, array $meta = array())
    {
        $table = $this->getTableName();
        $f = $this->fieldNames;

        $where = $this->where()
            ->equalTo("{$table}.{$f['sc_id']}", $shippingClassId)
            ->equalTo("{$table}.{$f['p_id']}",  $productId)
            ->equalTo("{$table}.{$f['w_id']}",  $this->getSiteId());

        $select = $this->getSelect($table)
            ->where($where)
            ->limit(1);

        $linker = $this->selectRows($select);
        $row = array(
            $f['sc_id'] => $shippingClassId,
            $f['p_id']  => $productId,
            $f['w_id']  => $this->getSiteId(),
            'meta'      => json_encode($meta)
        );
        if (count($linker) === 0) {
            $this->insert($row);
        } else {
            $this->update($row, $where);
        }
    }

    public function getProductShippingClass($productId)
    {
        $table = $this->getTableName();
        $t = $this->tableNames;
        $f = $this->fieldNames;

        $select = $this->getSelect($t['sc'])
            ->join(
                $table,
                $this->on($table, $t['sc'], $f['sc_id']),
                array('product_meta' => 'meta')
            );

        $where = $this->where()
            ->equalTo("{$table}.{$f['p_id']}", $productId);

        $select->where($where)
            ->limit(1);

        return $this->selectModel($select);
    }
}
