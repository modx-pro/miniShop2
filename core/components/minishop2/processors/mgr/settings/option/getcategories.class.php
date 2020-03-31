<?php

class msOptionGetCategoriesProcessor extends modObjectGetListProcessor
{
    public $classKey = 'modCategory';
    public $languageTopics = array('category');
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
}

return 'msOptionGetCategoriesProcessor';
