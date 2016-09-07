<?php

class msOrderProductGetListProcessor extends modObjectGetListProcessor
{
    public $classKey = 'msOrderProduct';
    public $languageTopics = array('minishop2:product');
    public $defaultSortField = 'id';
    public $defaultSortDirection = 'ASC';
    public $permission = 'msorder_list';


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
        $c->innerJoin('msOrder', 'msOrder', '`msOrderProduct`.`order_id` = `msOrder`.`id`');
        $c->leftJoin('msProduct', 'msProduct', '`msOrderProduct`.`product_id` = `msProduct`.`id`');
        $c->leftJoin('msProductData', 'msProductData', '`msOrderProduct`.`product_id` = `msProductData`.`id`');
        $c->leftJoin('msCategory', 'msCategory', '`msProduct`.`parent` = `msCategory`.`id`');
        $c->where(array(
            'order_id' => $this->getProperty('order_id'),
        ));

        $c->select($this->modx->getSelectColumns('msOrderProduct', 'msOrderProduct'));
        $c->select($this->modx->getSelectColumns('msProduct', 'msProduct', 'product_'));
        $c->select($this->modx->getSelectColumns('msProductData', 'msProductData', 'product_', array('id'), true));
        $c->select($this->modx->getSelectColumns('msCategory', 'msCategory', 'category_', array('id'), true));

        if ($query = $this->getProperty('query', null)) {
            $c->where(array(
                'msProduct.pagetitle:LIKE' => '%' . $query . '%',
                'OR:msProduct.description:LIKE' => '%' . $query . '%',
                'OR:msProduct.introtext:LIKE' => '%' . $query . '%',
                'OR:msProductData.article:LIKE' => '%' . $query . '%',
                'OR:msProductData.vendor:LIKE' => '%' . $query . '%',
                'OR:msProductData.made_in:LIKE' => '%' . $query . '%',
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
        $fields = array_map('trim', explode(',', $this->modx->getOption('ms2_order_product_fields', null, '')));
        $fields = array_values(array_unique(array_merge($fields, array(
            'id',
            'product_id',
            'name',
            'product_pagetitle',
        ))));

        $data = array();
        foreach ($fields as $v) {
            $data[$v] = $object->get($v);
            if ($v == 'product_price' || $v == 'product_old_price') {
                $data[$v] = round($data[$v], 2);
            } else {
                if ($v == 'product_weight') {
                    $data[$v] = round($data[$v], 3);
                }
            }
        }

        $data['name'] = !$object->get('name')
            ? $object->get('product_pagetitle')
            : $object->get('name');

        $options = $object->get('options');
        if (!empty($options) && is_array($options)) {
            $tmp = array();
            foreach ($options as $k => $v) {
                $tmp[] = $this->modx->lexicon('ms2_' . $k) . ': ' . $v;
                $data['option_' . $k] = $v;
            }
            $data['options'] = implode('; ', $tmp);
        }

        $data['actions'] = array(
            array(
                'cls' => '',
                'icon' => 'icon icon-edit',
                'title' => $this->modx->lexicon('ms2_menu_update'),
                'action' => 'updateOrderProduct',
                'button' => true,
                'menu' => true,
            ),
            array(
                'cls' => array(
                    'menu' => 'red',
                    'button' => 'red',
                ),
                'icon' => 'icon icon-trash-o',
                'title' => $this->modx->lexicon('ms2_menu_remove'),
                'multiple' => $this->modx->lexicon('ms2_menu_remove'),
                'action' => 'removeOrderProduct',
                'button' => true,
                'menu' => true,
            ),
        );

        return $data;
    }

}

return 'msOrderProductGetListProcessor';