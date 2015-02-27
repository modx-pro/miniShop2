<?php

class msFeatureGetProcessor extends modObjectGetProcessor {
    public $classKey = 'msFeature';
    public $objectType = 'ms2_feature';
    public $languageTopics = array('minishop2:default');

    /**
     * Used for adding custom data in derivative types
     * @return void
     */
    public function beforeOutput() {
        $c = $this->modx->newQuery('msCategory');
        $c->leftJoin('msCategoryFeature', 'msCategoryFeature', 'msCategoryFeature.category_id=msCategory.id');
        $c->where(array('msCategoryFeature.feature_id' => $this->object->get('id')));
        $c->select(array(
            $this->modx->getSelectColumns('msCategory', 'msCategory'),
            $this->modx->getSelectColumns('msCategoryFeature','msCategoryFeature','',array('id', 'feature_id', 'category_id'), true),
        ));
        $categories = $this->modx->getIterator('msCategory', $c);

        $data = array();
        /** @var msCategory $category */
        foreach ($categories as $category) {
            $data[] = $category->toArray();
        }
        $this->object->set('categories', $data);

        $properties = $this->object->getInputProperties();
        $this->object->set('properties', $properties);
    }
}

return 'msFeatureGetProcessor';
