<?php

class msPaymentDeliveriesGetListProcessor extends modObjectGetListProcessor
{
    public $classKey = 'msDelivery';
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
        $c->leftJoin('msDeliveryMember', 'Payments',
            "Payments.delivery_id = {$this->classKey}.id AND Payments.payment_id = {$this->getProperty('payment')}"
        );
        $c->select($this->modx->getSelectColumns($this->classKey, $this->classKey));
        $c->select('(Payments.delivery_id is not null) as active');
        $c->groupby($this->classKey . '.id');

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

        if (empty($data['active'])) {
            $data['actions'][] = array(
                'cls' => '',
                'icon' => 'icon icon-power-off action-green',
                'title' => $this->modx->lexicon('ms2_menu_enable'),
                'multiple' => $this->modx->lexicon('ms2_menu_enable'),
                'action' => 'enableDelivery',
                'button' => true,
                'menu' => true,
            );
        } else {
            $data['actions'][] = array(
                'cls' => '',
                'icon' => 'icon icon-power-off action-gray',
                'title' => $this->modx->lexicon('ms2_menu_disable'),
                'multiple' => $this->modx->lexicon('ms2_menu_disable'),
                'action' => 'disableDelivery',
                'button' => true,
                'menu' => true,
            );
        }

        return $data;
    }

}

return 'msPaymentDeliveriesGetListProcessor';