<?php

namespace SpeckShipping\Mapper;

class Category extends AbstractMapper
{
    protected $tableName = 'shipping_class_category';

    public function __construct()
    {
        parent::__construct();
        $this->hydrator        = $this->hydrators['array'];
        $this->entityPrototype = $this->entityPrototypes['array'];
    }

    public function linkShippingClass($shippingClassId, $categoryId, array $meta = array())
    {
        $table = $this->getTableName();
        $f = $this->fieldNames;

        $where = $this->where()
            ->equalTo("{$table}.{$f['sc_id']}", $shippingClassId)
            ->equalTo("{$table}.{$f['c_id']}",  $categoryId)
            ->equalTo("{$table}.{$f['w_id']}",  $this->getSiteId());

        $select = $this->select($table)
            ->where($where)
            ->limit(1);

        $linker = $this->selectRows($select);
        if (count($linker) === 0) {
            $row = array(
                $f['sc_id'] => $sc->getClassId(),
                $f['c_id']  => $categoryId,
                $f['w_id']  => $this->getSiteId(),
                'meta'      => json_encode($meta)
            );
            $this->insert($row);
        }
    }

    public function getShippingClassForCategory($categoryId)
    {
        $table = $this->getTableName();
        $t = $this->tableNames;
        $f = $this->fieldNames;

        $select = $this->getSelect($t['c_c_w'])
            ->join(
                $table,
                $this->on($table, $t['c_c_w'], $f['c_id']),
                array(
                    'shipping_class_id' => 'shipping_class_id',
                    'category_meta'     => 'meta'
                ),
                'left'
            );

        $where = $this->where()
            ->equalTo("{$t['c_c_w']}.{$f['c_id']}", $categoryId)
            ->equalTo("{$t['c_c_w']}.{$f['w_id']}", $this->getSiteId());

            $select->where($where)
                ->limit(1);

        return $this->selectModel($select);
    }

    //get parent categories (array of rows)
    //joined with category shipping class
    public function getParentCategoriesForCategory($categoryId)
    {
        $table = $this->getTableName();
        $t = $this->tableNames;
        $f = $this->fieldNames;

        $select = $this->getSelect($t['c_c_w'])
            ->join(
                $table,
                $this->on($table, $t['c_c_w'], $f['c_id']),
                array(
                    'shipping_class_id' => 'shipping_class_id',
                    'category_meta'     => 'meta'
                ),
                'left'
            );

        $where = $this->where()
            ->equalTo("{$t['c_c_w']}.{$f['c_id']}", $categoryId)
            ->equalTo("{$t['c_c_w']}.{$f['w_id']}", $this->getSiteId());

        $select->where($where);

        return $this->selectRows($select);
    }

    //get parent categories (array of rows)
    //joined with category shipping class
    public function getParentCategoriesForProduct($productId)
    {
        $table = $this->getTableName();
        $t = $this->tableNames;
        $f = $this->fieldNames;

        $select = $this->getSelect($t['c_c_p'])
            ->join(
                $table,
                $this->on($table, $t['c_c_p'], $f['c_id']),
                array(
                    'shipping_class_id' => 'shipping_class_id',
                    'category_meta'     => 'meta'
                ),
                'left'
            );

        $where = $this->where()
            ->equalTo("{$t['c_c_p']}.{$f['p_id']}", $productId)
            ->equalTo("{$t['c_c_p']}.{$f['w_id']}", $this->getSiteId());

        $select->where($where);

        return $this->selectRows($select);
    }
}
