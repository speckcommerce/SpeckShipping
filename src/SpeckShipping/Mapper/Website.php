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

    public function linkShippingClass($shippingClassId, $siteId, array $meta = array())
    {
        $table = $this->getTableName();
        $f = $this->fieldNames;

        $where = new Where();
        $where->equalTo("{$table}.{$f['sc_id']}", $shippingClassId)
              ->equalTo("{$table}.{$f['w_id']}",  $siteId);

        $select = $this->getSelect($table)
            ->where($where)
            ->limit(1);

        $linker = $this->selectRows($select);
        if (count($linker) === 0) {
            $row = array(
                $f['sc_id'] => $sc->getClassId(),
                $f['w_id']  => $siteId,
                'meta'      => $meta
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
                $this->on($table, $t['sc'], $f['sc_id']),
                array(
                    'shipping_class_id' => 'shipping_class_id',
                    'site_meta'         => 'meta',
                )
            );

        $where  = $this->where()
            ->equalTo("{$t['w_sc']}.{$f['w_id']}", $this->getSiteId());

        $select->where($where)
            ->limit(1);

        return $this->selectModel($select);
    }
}
