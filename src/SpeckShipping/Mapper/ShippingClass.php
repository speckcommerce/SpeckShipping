<?php

namespace SpeckShipping\Mapper;

use SpeckShipping\Entity\ShippingClassInterface;

class ShippingClass extends AbstractMapper
{
    protected $tableName = 'shipping_class';

    public function __construct()
    {
        parent::__construct();
        $this->hydrator        = $this->hydrators['sc'];
        $this->entityPrototype = $this->entityPrototypes['sc'];
    }
    public function getShippingClassById($id)
    {
        $t = $this->tableNames;
        $f = $this->fieldNames;

        $select = $this->getSelect();

        $where  = $this->where()
            ->equalTo("{$t['sc']}.{$f['sc_id']}", $id);

        $select->where($where)
            ->limit(1);

        return $this->selectModel($select);
    }

    public function persist(ShippingClassInterface $sc, $where = null)
    {
        if (
            is_numeric($sc->getClassId())
            && $this->getShippingClassById($sc->getClassId())
        ){
            return $this->updateShippingClass($sc);
        }

        return $this->insertShippingClass($sc);
    }

    public function updateShippingClass(ShippingClassInterface $sc, $where = null)
    {
        $scId = $this->fieldNames['sc_id'];
        $where = $where ?: array($scId => $sc->getClassId());

        return parent::update($sc, $where);
    }

    public function insertShippingClass(ShippingClassInterface $sc)
    {
        $resp = parent::insert($sc);

        //todo: get insert id
        return $sc->setClassId($id);
    }
}
