<?php

class msOptionGetProcessor extends modObjectGetProcessor {
    public $classKey = 'msOption';
    public $objectType = 'ms2_option';
    public $languageTopics = array('minishop2:default');

    /**
     * Used for adding custom data in derivative types
     * @return void
     */
    public function beforeOutput() {
        $c = $this->modx->newQuery('msCategory');
        $c->leftJoin('msCategoryOption', 'msCategoryOption', 'msCategoryOption.category_id=msCategory.id');
        $c->where(array('msCategoryOption.option_id' => $this->object->get('id')));
        $c->select(array(
            $this->modx->getSelectColumns('msCategory', 'msCategory'),
            $this->modx->getSelectColumns('msCategoryOption','msCategoryOption','',array('id', 'option_id', 'category_id'), true),
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

return 'msOptionGetProcessor';
