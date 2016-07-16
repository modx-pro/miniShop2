<?php

class msPaymentGetListProcessor extends modObjectGetListProcessor
{
    public $classKey = 'msPayment';
    public $defaultSortField = 'rank';
    public $defaultSortDirection = 'asc';
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
            $c->where(array('active' => 1));
        }
        else {
            $c->leftJoin('msDeliveryMember', 'Deliveries');
            $c->groupby($this->classKey . '.id');
            $c->select($this->modx->getSelectColumns($this->classKey, $this->classKey));
            $c->select('COUNT(Deliveries.delivery_id) as deliveries');
        }
        if ($query = trim($this->getProperty('query'))) {
            $c->where(array(
                'name:LIKE' => "%{$query}%",
                'OR:description:LIKE' => "%{$query}%",
                'OR:class:LIKE' => "%{$query}%",
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
        $data['actions'] = array();

        $data['actions'][] = array(
            'cls' => '',
            'icon' => 'icon icon-edit',
            'title' => $this->modx->lexicon('ms2_menu_update'),
            'action' => 'updatePayment',
            'button' => true,
            'menu' => true,
        );
        if (empty($data['active'])) {
            $data['actions'][] = array(
                'cls' => '',
                'icon' => 'icon icon-power-off action-green',
                'title' => $this->modx->lexicon('ms2_menu_enable'),
                'multiple' => $this->modx->lexicon('ms2_menu_enable'),
                'action' => 'enablePayment',
                'button' => true,
                'menu' => true,
            );
        } else {
            $data['actions'][] = array(
                'cls' => '',
                'icon' => 'icon icon-power-off action-gray',
                'title' => $this->modx->lexicon('ms2_menu_disable'),
                'multiple' => $this->modx->lexicon('ms2_menu_disable'),
                'action' => 'disablePayment',
                'button' => true,
                'menu' => true,
            );
        }
        $data['actions'][] = array(
            'cls' => array(
                'menu' => 'red',
                'button' => 'red',
            ),
            'icon' => 'icon icon-trash-o',
            'title' => $this->modx->lexicon('ms2_menu_remove'),
            'multiple' => $this->modx->lexicon('ms2_menu_remove_multiple'),
            'action' => 'removePayment',
            'button' => true,
            'menu' => true,
        );

        return $data;
    }

}

return 'msPaymentGetListProcessor';