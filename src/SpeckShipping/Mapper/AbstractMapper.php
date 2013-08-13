<?php

namespace SpeckShipping\Mapper;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Where;
use Zend\Db\Sql\Select;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
use SpeckShipping\Entity\ShippingClass as Entity;
use SpeckShipping\Entity\ShippingClassHydrator;
use ZfcBase\Mapper\AbstractDbMapper;
use Zend\Stdlib\Hydrator\ArraySerializable as ArrayHydrator;
use Zend\Stdlib\ArrayObject;

class AbstractMapper extends AbstractDbMapper
    implements ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    protected $tableNames = array(
        'sc'    => 'shipping_class',
        'p_sc'  => 'shipping_class_product',
        'c_sc'  => 'shipping_class_category',
        'w_sc'  => 'shipping_class_website',
        'c_c_w' => 'catalog_category_website',
        'c_c_p' => 'catalog_category_product',
    );

    protected $fieldNames = array(
        'sc_id' => 'shipping_class_id',
        'c_id'  => 'category_id',
        'p_id'  => 'product_id',
        'w_id'  => 'website_id',
    );

    public function __construct()
    {
        $this->hydrators = array(
            'array' => new ArrayHydrator(),
            'sc'    => new ShippingClassHydrator(),
        );
        $this->entityPrototypes = array(
            'array' => new ArrayObject,
            'sc'    => new Entity,
        );
    }

    public function selectModel(Select $select)
    {
        $entityPrototype = $this->entityPrototypes['sc'];
        $hydrator        = $this->hydrators['sc'];

        return $this->select($select, $entityPrototype, $hydrator)->current();
    }

    public function selectRows(Select $select)
    {
        $entityPrototype = $this->entityPrototypes['array'];
        $hydrator        = $this->hydrators['array'];

        $return = array();
        foreach ($this->select($select, $entityPrototype, $hydrator) as $row) {
            $return[] = $row;
        }
        return $return;
    }

    public function on($table1, $table2, $field1, $field2=null)
    {
        $field2 = $field2 ?: $field1;
        return "{$table1}.{$field1} = {$table2}.{$field2}";
    }

    public function where()
    {
        return new Where();
    }

    public function getSiteId()
    {
        //todo : get from multisite
        return 1;
    }
}
