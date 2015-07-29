<?php

class msOptionGetListProcessor extends modObjectGetListProcessor {
    public $classKey = 'msOption';
    public $defaultSortField = 'msOption.key';
    public $defaultSortDirection  = 'asc';
    public $objectType = 'ms2';
    public $languageTopics = array('minishop2:default');

    public function prepareQueryBeforeCount(xPDOQuery $c) {

        $query = $this->getProperty('query', '');
        if (!empty($query)) {
            $c->where(array(
                'msOption.key:LIKE' => "%{$query}%",
                'OR:msOption.caption:LIKE' => "%{$query}%",
            ));
        }

        $category = (int)$this->getProperty('category', 0);
        $categories = $this->getProperty('categories', '[]');
        $categories = $this->modx->fromJSON($categories);

        if (($category > 0) || (count($categories) > 0)) {
            $c->leftJoin('msCategoryOption', 'msCategoryOption', 'msCategoryOption.option_id=msOption.id');
            $c->select(array(
                $this->modx->getSelectColumns('msOption','msOption'),
                $this->modx->getSelectColumns('msCategoryOption', 'msCategoryOption','', array('id', 'option_id'), true),
            ));
        }

        if ($category > 0) {
            $c->where(array(
                'msCategoryOption.category_id' => $category
            ));
        }

        if (count($categories) > 0) {
            $c->where(array(
                'msCategoryOption.category_id:IN' => $categories
            ));
        }

        return $c;
    }

    public function prepareRow(xPDOObject $object) {
        $data = $object->toArray();
        $categories = $object->getMany('OptionCategories');
        $data['categories'] = array();
        /** @var msCategoryOption $cat */
        foreach ($categories as $cat) {
            $category = $cat->getOne('Category');
            if ($category) {
                $data['categories'][] = $category->get(array('id', 'pagetitle'));
            }
        }
        return $data;
    }
}

return 'msOptionGetListProcessor';
