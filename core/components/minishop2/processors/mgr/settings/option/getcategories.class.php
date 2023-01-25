<?php

class msOptionGetCategoriesProcessor extends modObjectGetListProcessor
{
    public $classKey = 'modCategory';
    public $languageTopics = ['category'];
    public $defaultSortField = 'category';
    public $permission = 'view_category';

    /**
     * @param xPDOQuery $c
     *
     * @return xPDOQuery
     */
    public function prepareQueryBeforeCount(xPDOQuery $c)
    {
        $c->innerJoin('msOption', 'msOption', 'msOption.category=modCategory.id');

        return $c;
    }

    /**
     * @param array $list
     *
     * @return array
     */
    public function afterIteration(array $list)
    {
        array_unshift($list, [
            'id' => 0,
            'category' => $this->modx->lexicon('no_category'),
        ]);

        return $list;
    }
}

return 'msOptionGetCategoriesProcessor';
