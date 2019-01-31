<?php

class msOptionGetProcessor extends modObjectGetProcessor
{
    /** @var msOption $object */
    public $object;
    public $classKey = 'msOption';
    public $objectType = 'ms2_option';
    public $languageTopics = array('minishop2:default');
    public $permission = 'mssetting_view';


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
     * @return void
     */
    public function beforeOutput()
    {
        $c = $this->modx->newQuery('msCategory');
        $c->leftJoin('msCategoryOption', 'msCategoryOption', 'msCategoryOption.category_id = msCategory.id');
        $c->where(array('msCategoryOption.option_id' => $this->object->get('id')));
        $c->select(array(
            $this->modx->getSelectColumns('msCategory', 'msCategory'),
            $this->modx->getSelectColumns('msCategoryOption', 'msCategoryOption', '',
                array('id', 'option_id', 'category_id'), true),
        ));
        $categories = $this->modx->getIterator('msCategory', $c);

        $data = array();
        /** @var msCategory $category */
        foreach ($categories as $category) {
            $data[] = $category->toArray();
        }
        $this->object->set('categories', $data);

        $data = $parents = array();
        $categories = $this->object->getIterator('OptionCategories');
        /** @var msCategoryOption $cat */
        foreach ($categories as $cat) {
            $category = $cat->getOne('Category');
            if ($category) {
                $data[] = $category->get('id');
                $parentIds = $this->modx->getParentIds($category->get('id'), 10, array('context' => $category->get('context_key')));
                $parents = array_merge($parents, $parentIds);
            }
        }
        $this->object->set('tree_parents', json_encode(array_values(array_unique($parents))));
        $this->object->set('categories', json_encode($data));
        $this->object->set('properties', $this->object->getInputProperties());
    }
}

return 'msOptionGetProcessor';
