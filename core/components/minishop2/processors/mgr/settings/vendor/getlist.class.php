<?php

class msVendorGetListProcessor extends modObjectGetListProcessor
{
    public $classKey = 'msVendor';
    public $defaultSortField = 'id';
    public $defaultSortDirection = 'ASC';
    public $permission = 'mssetting_list';


    /**
     * @return bool|null|string
     */
    public function initialize()
    {
        if (!$this->modx->hasPermission($this->permission)) {
            return $this->modx->lexicon('access_denied');
        }

        return parent::initialize();
    }


    /**
     * @param xPDOQuery $c
     *
     * @return xPDOQuery
     */
    public function prepareQueryBeforeCount(xPDOQuery $c)
    {
        if ($this->getProperty('combo')) {
            $c->select('id,name');
        }
        else {
            $c->leftJoin('modResource', 'Resource');
            $c->select($this->modx->getSelectColumns($this->classKey, $this->classKey));
            $c->select('Resource.pagetitle');
        }
        if ($id = (int)$this->getProperty('id')) {
            $c->where(array('id' => $id));
        }
        if ($query = trim($this->getProperty('query'))) {
            $c->where(array(
                'name:LIKE' => "%{$query}%",
                'OR:description:LIKE' => "%{$query}%",
                'OR:country:LIKE' => "%{$query}%",
                'OR:email:LIKE' => "%{$query}%",
                'OR:address:LIKE' => "%{$query}%",
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
        if ($this->getProperty('combo')) {
            $data = array(
                'id' => $object->get('id'),
                'name' => $object->get('name'),
            );
        } else {
            $data = $object->toArray();
            if (!$data['resource']) {
                $data['resource'] = null;
            }
            $data['actions'] = array();

            $data['actions'][] = array(
                'cls' => '',
                'icon' => 'icon icon-edit',
                'title' => $this->modx->lexicon('ms2_menu_update'),
                'action' => 'updateVendor',
                'button' => true,
                'menu' => true,
            );

            $data['actions'][] = array(
                'cls' => array(
                    'menu' => 'red',
                    'button' => 'red',
                ),
                'icon' => 'icon icon-trash-o',
                'title' => $this->modx->lexicon('ms2_menu_remove'),
                'multiple' => $this->modx->lexicon('ms2_menu_remove_multiple'),
                'action' => 'removeVendor',
                'button' => true,
                'menu' => true,
            );

        }

        return $data;
    }

}

return 'msVendorGetListProcessor';