<?php

class msOrderStatusGetListProcessor extends modObjectGetListProcessor
{
    public $classKey = 'msOrderStatus';
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
        if ($query = trim($this->getProperty('query'))) {
            $c->where(array(
                'name:LIKE' => "%{$query}%",
                'OR:description:LIKE' => "%{$query}%",
            ));
        }
        if ($this->getProperty('combo')) {
            $c->select('id,name');
            $c->where(array('active' => 1));

            if ($order_id = $this->getProperty('order_id')) {
                /** @var msOrder $order */
                if ($order = $this->modx->getObject('msOrder', $order_id)) {
                    /** @var msOrderStatus $status */
                    $status = $order->getOne('Status');
                    if ($status->get('final')) {
                        $c->where(array('id' => $status->get('id')));
                    } elseif ($status->get('fixed')) {
                        $c->where(array('rank:>=' => $status->get('rank')));
                    }
                }
            }
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
            if (!$data['body_user']) {
                $data['body_user'] = null;
            }
            if (!$data['body_manager']) {
                $data['body_manager'] = null;
            }
            $data['actions'] = array();

            $data['actions'][] = array(
                'cls' => '',
                'icon' => 'icon icon-edit',
                'title' => $this->modx->lexicon('ms2_menu_update'),
                'action' => 'updateStatus',
                'button' => true,
                'menu' => true,
            );

            if (empty($data['active'])) {
                $data['actions'][] = array(
                    'cls' => '',
                    'icon' => 'icon icon-power-off action-green',
                    'title' => $this->modx->lexicon('ms2_menu_enable'),
                    'multiple' => $this->modx->lexicon('ms2_menu_enable'),
                    'action' => 'enableStatus',
                    'button' => true,
                    'menu' => true,
                );
            } else {
                $data['actions'][] = array(
                    'cls' => '',
                    'icon' => 'icon icon-power-off action-gray',
                    'title' => $this->modx->lexicon('ms2_menu_disable'),
                    'multiple' => $this->modx->lexicon('ms2_menu_disable'),
                    'action' => 'disableStatus',
                    'button' => true,
                    'menu' => true,
                );
            }
            if ($data['editable']) {
                $data['actions'][] = array(
                    'cls' => array(
                        'menu' => 'red',
                        'button' => 'red',
                    ),
                    'icon' => 'icon icon-trash-o',
                    'title' => $this->modx->lexicon('ms2_menu_remove'),
                    'multiple' => $this->modx->lexicon('ms2_menu_remove_multiple'),
                    'action' => 'removeStatus',
                    'button' => true,
                    'menu' => true,
                );
            }
        }

        return $data;
    }


    /**
     * @param array $array
     * @param bool $count
     *
     * @return string
     */
    public function outputArray(array $array, $count = false)
    {
        if ($this->getProperty('addall')) {
            $array = array_merge_recursive(array(
                array(
                    'id' => 0,
                    'name' => $this->modx->lexicon('ms2_all'),
                ),
            ), $array);
        }

        return parent::outputArray($array, $count);
    }

}

return 'msOrderStatusGetListProcessor';