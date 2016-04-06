<?php

class msCategoryOptionGetListProcessor extends modObjectGetListProcessor
{
    public $classKey = 'msCategoryOption';
    public $defaultSortField = 'msCategoryOption.key';
    public $defaultSortDirection = 'asc';
    public $languageTopics = array('minishop2:default');


    /**
     * @param xPDOQuery $c
     *
     * @return xPDOQuery
     */
    public function prepareQueryBeforeCount(xPDOQuery $c)
    {
        $category = (int)$this->getProperty('category', 0);
        $c->where(array(
            'category_id' => $category,
        ));
        $c->innerJoin('msOption', 'Option');
        $c->select($this->modx->getSelectColumns('msCategoryOption', 'msCategoryOption'));
        $c->select($this->modx->getSelectColumns('msOption', 'Option'));

        $query = trim($this->getProperty('query'));
        if (!empty($query)) {
            $c->where(array(
                'Option.key:LIKE' => "%{$query}%",
                'OR:Option.caption:LIKE' => "%{$query}%",
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
        $array = $object->toArray();
        $array['actions'] = array();

        if (!$array['active']) {
            $array['actions'][] = array(
                'cls' => '',
                'icon' => 'icon icon-power-off action-green',
                'title' => $this->modx->lexicon('ms2_ft_selected_activate'),
                'multiple' => $this->modx->lexicon('ms2_ft_selected_activate'),
                'action' => 'activateOption',
                'button' => true,
                'menu' => true,
            );
        } else {
            $array['actions'][] = array(
                'cls' => '',
                'icon' => 'icon icon-power-off action-gray',
                'title' => $this->modx->lexicon('ms2_ft_selected_deactivate'),
                'multiple' => $this->modx->lexicon('ms2_ft_selected_deactivate'),
                'action' => 'deactivateOption',
                'button' => true,
                'menu' => true,
            );
        }

        if (!$array['required']) {
            $array['actions'][] = array(
                'cls' => '',
                'icon' => 'icon icon-bolt action-yellow',
                'title' => $this->modx->lexicon('ms2_ft_selected_require'),
                'multiple' => $this->modx->lexicon('ms2_ft_selected_require'),
                'action' => 'requireOption',
                'button' => true,
                'menu' => true,
            );
        } else {
            $array['actions'][] = array(
                'cls' => '',
                'icon' => 'icon icon-bolt action-gray',
                'title' => $this->modx->lexicon('ms2_ft_selected_unrequire'),
                'multiple' => $this->modx->lexicon('ms2_ft_selected_unrequire'),
                'action' => 'unrequireOption',
                'button' => true,
                'menu' => true,
            );
        }


        $array['actions'][] = array(
            'cls' => '',
            'icon' => 'icon icon-trash-o',
            'title' => $this->modx->lexicon('ms2_ft_selected_delete'),
            'multiple' => $this->modx->lexicon('ms2_ft_selected_delete'),
            'action' => 'deleteOption',
            'button' => true,
            'menu' => true,
        );

        return $array;
    }
}

return 'msCategoryOptionGetListProcessor';
