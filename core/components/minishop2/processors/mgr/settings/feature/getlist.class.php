<?php

class msFeatureGetListProcessor extends modObjectGetListProcessor {
    public $classKey = 'msFeature';
    public $defaultSortField = 'msFeature.name';
    public $defaultSortDirection  = 'asc';
    public $objectType = 'ms2';
    public $languageTopics = array('minishop2:default');

    public function prepareQueryBeforeCount(xPDOQuery $c) {

        $query = $this->getProperty('query', '');
        if (!empty($query)) {
            $c->where(array(
                'msFeature.name:LIKE' => "%{$query}%",
                'OR:msFeature.caption:LIKE' => "%{$query}%",
            ));
        }

        $c->leftJoin('msCategoryFeature', 'msCategoryFeature', 'msCategoryFeature.feature_id=msFeature.id');
        $c->select(array(
            $this->modx->getSelectColumns('msFeature','msFeature'),
            $this->modx->getSelectColumns('msCategoryFeature', 'msCategoryFeature','', array('id', 'feature_id'), true),
        ));

        $category = (int)$this->getProperty('category', 0);
        if ($category > 0) {
            $c->where(array(
                'msCategoryFeature.category_id' => $category
            ));
        }

        return $c;
    }

    public function prepareRow(xPDOObject $object) {
        $data = $object->toArray();
        $categories = $object->getMany('FeatureCategories');
        $data['categories'] = array();
        /** @var msCategoryFeature $cat */
        foreach ($categories as $cat) {
            $category = $cat->getOne('Category');
            if ($category) {
                $data['categories'][] = $category->get(array('id', 'pagetitle'));
            }
        }
        return $data;
    }
}

return 'msFeatureGetListProcessor';
