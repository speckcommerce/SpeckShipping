<?php

namespace SpeckShipping\Mapper;

class Website extends AbstractMapper
{
    protected $tableName = 'shipping_class_website';

    public function __construct()
    {
        parent::__construct();
        $this->hydrator        = $this->hydrators['array'];
        $this->entityPrototype = $this->entityPrototypes['array'];
    }

    public function linkShippingClass($sc, $siteId)
    {
        $table = $this->getTableName();
        $f = $this->fieldNames;

        $where = new Where();
        $where->equalTo("{$table}.{$f['sc_id']}", $sc->getClassId())
              ->equalTo("{$table}.{$f['w_id']}",  $siteId);

        $select = $this->getSelect($table)
            ->where($where)
            ->limit(1);

        $linker = $this->selectRows($select);
        if (count($linker) === 0) {
            $row = array(
                $f['sc_id'] => $sc->getClassId(),
                $f['w_id']  => $siteId
            );
            $this->insert($row);
        }
    }

    public function getSiteShippingClass()
    {
        $table = $this->getTableName();
        $t = $this->tableNames;
        $f = $this->fieldNames;

        $select = $this->getSelect($t['sc'])
            ->join(
                $table,
                $this->on($table, $t['sc'], $f['sc_id'])
            );

        $where  = $this->where()
            ->equalTo("{$t['w_sc']}.{$f['w_id']}", $this->getSiteId());

        $select->where($where)
            ->limit(1);

        return $this->selectModel($select);
    }
}
