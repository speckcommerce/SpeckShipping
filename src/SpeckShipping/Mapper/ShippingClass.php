<?php

namespace SpeckShipping\Mapper;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Where;
use Zend\Db\Sql\Select;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
use SpeckShipping\Entity\ShippingClass as Entity;
use SpeckShipping\Entity\ShippingClassHydrator;

class ShippingClass implements ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    protected $adapter = 'speckshipping_db';

    protected $tableNames = array(
        'sc'    => 'shipping_class',
        'p_sc'  => 'shipping_class_product',
        'c_sc'  => 'shipping_class_category',
        's_sc'  => 'shipping_class_site',
        'c_c_w' => 'catalog_category_website',
        'c_c_p' => 'catalog_category_product',
    );

    protected $fieldNames = array(
        'sc_id' => 'shipping_class_id',
        'c_id'  => 'category_id',
        'p_id'  => 'product_id',
        's_id'  => 'site_id',
    );

    protected $tgs = array();

    public function getTableGateway($tableName)
    {
        $adapter = $this->getServiceLocator()->get($this->adapter);
        if (!isset($this->tgs[$tableName])) {
            $this->tgs[$tableName] = new TableGateway($tableName, $adapter);
        }
        return $this->tgs[$tableName];
    }

    public function getEntity(array $data = null)
    {
        if (null === $data) {
            return new Entity();
        }
        $hydrator = new ShippingClassHydrator();
        return $hydrator->hydrate($data, new Entity());
    }

    public function select($table = null)
    {
        $table = $table ?: $this->tableNames['sc'];

        return new Select($table);
    }

    public function selectOneEntity($select, $tg = null)
    {
        $tg = $tg ?: $this->getTableGateway($this->tableNames['sc']);

        $result = $tg->selectWith($select)->toArray();
        if (count($result)) {
            $data = array_pop($result);
            return $this->getEntity($data);
        }
        return false;
    }

    public function selectManyRows($select, $tg = null)
    {
        $tg = $tg ?: $this->getTableGateway($this->tableNames['sc']);

        return $tg->selectWith($select)->toArray();
    }

    //add a join with 'on' string, without bulding the string by hand
    public function ezJoin($select, $table1, $table2, $field1, $field2=null)
    {
        $field2 = $field2 ?: $field1;
        $select->join($table1, "{$table1}.{$field1} = {$table2}.{$field2}");
    }

    public function getShippingClassById($id)
    {
        $t = $this->tableNames;
        $f = $this->fieldNames;

        $select = $this->select();

        $where  = new Where();
        $where->equalTo("{$t['sc']}.{$f['sc_id']}", $id);
        $select->where($where);

        $select->limit(1);

        return $this->selectOneEntity($select);
    }

    //get parent categories (array of rows)
    //joined with category shipping class
    public function getParentCategoriesForCategory($categoryId)
    {
        $t = $this->tableNames;
        $f = $this->fieldNames;

        $select = $this->select();
        $this->ezJoin($select, $t['c_sc'],  $t['sc'],    $f['sc_id']);
        $this->ezJoin($select, $t['c_c_p'], $t['c_c_w'], $f['c_id']);

        $where  = new Where();
        $where->equalTo("{$t['c_c_p']}.{$f['c_id']}", $categoryId);
        $select->where($where);

        return $this->selectManyRows($select);
    }

    //get parent categories (array of rows)
    //joined with category shipping class
    public function getParentCategoriesForProduct($productId)
    {
        $t = $this->tableNames;
        $f = $this->fieldNames;

        $select = $this->select();
        $this->ezJoin($select, $t['c_sc'],  $t['sc'],   $f['sc_id']);
        $this->ezJoin($select, $t['c_c_p'], $t['c_sc'], $f['c_id']);

        $where  = new Where();
        $where->equalTo("{$t['c_c_p']}.{$f['p_id']}", $productId);
        $select->where($where);

        return $this->selectManyRows($select);
    }

    public function getProductShippingClass($productId)
    {
        $t = $this->tableNames;
        $f = $this->fieldNames;

        $select = $this->select();
        $this->ezJoin($select, $t['p_sc'], $t['sc'], $f['sc_id']);

        $where  = new Where();
        $where->equalTo("{$t['p_sc']}.{$f['p_id']}", $productId);
        $select->where($where);

        $select->limit(1);

        return $this->selectOneEntity($select);
    }

    public function getSiteShippingClass()
    {
        $t = $this->tableNames;
        $f = $this->fieldNames;

        $select = $this->select();
        $this->ezJoin($select, $t['s_sc'], $t['sc'], $f['sc_id']);

        $where  = new Where();
        $where->equalTo("{$t['s_sc']}.{$f['s_id']}", $this->getSiteId());
        $select->where($where);

        $select->limit(1);

        return $this->selectOneEntity($select);
    }

    public function getSiteId()
    {
        //todo : get from multisite
        return 1;
    }
}
