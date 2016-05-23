<?php

class msProductLinkGetListProcessor extends modObjectGetListProcessor
{
    public $classKey = 'msProductLink';
    public $defaultSortField = 'link';
    public $defaultSortDirection = 'ASC';
    public $permission = '';


    /**
     * @param xPDOQuery $c
     *
     * @return xPDOQuery
     */
    public function prepareQueryBeforeCount(xPDOQuery $c)
    {
        if ($master = $this->getProperty('master')) {
            $c->orCondition(array('master' => $master, 'slave' => $master));
        }
        $c->innerJoin('msLink', 'msLink', 'msProductLink.link=msLink.id');
        $c->leftJoin('msProduct', 'Master', 'Master.id=msProductLink.master');
        $c->leftJoin('msProduct', 'Slave', 'Slave.id=msProductLink.slave');
        $c->select($this->modx->getSelectColumns('msProductLink', 'msProductLink'));
        $c->select($this->modx->getSelectColumns('msLink', 'msLink', '', array('id'), true));
        $c->select('Master.pagetitle as master_pagetitle, Slave.pagetitle as slave_pagetitle');

        $query = trim($this->getProperty('query'));
        if (!empty($query)) {
            $c->where(array(
                'msLink.name:LIKE' => "%{$query}%",
                'OR:Master.pagetitle:LIKE' => "%{$query}%",
                'OR:Slave.pagetitle:LIKE' => "%{$query}%",
            ));
        }

        return $c;
    }


    /**
     * @param xPDOObject $object
     *
     * @return array
     */
    public function prepareRow(xPDOObject $object)
    {
        $data = $object->toArray();

        $data['actions'] = array(
            array(
                'cls' => array(
                    'menu' => 'red',
                    'button' => 'red',
                ),
                'icon' => 'icon icon-trash-o',
                'title' => $this->modx->lexicon('ms2_menu_remove'),
                'multiple' => $this->modx->lexicon('ms2_menu_remove_multiple'),
                'action' => 'removeLink',
                'button' => true,
                'menu' => true,
            ),
        );

        return $data;
    }


}

return 'msProductLinkGetListProcessor';