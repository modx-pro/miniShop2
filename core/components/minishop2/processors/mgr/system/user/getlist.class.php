<?php

class msUserGetListProcessor extends modObjectGetListProcessor
{
    public $classKey = 'modUser';
    public $languageTopics = array('user');
    public $defaultSortField = 'username';


    /**
     * @param xPDOQuery $c
     *
     * @return xPDOQuery
     */
    public function prepareQueryBeforeCount(xPDOQuery $c)
    {
        $c->leftJoin('modUserProfile', 'Profile');

        $query = $this->getProperty('query', '');
        if (!empty($query)) {
            $c->where(array(
                'modUser.username:LIKE' => "%{$query}%",
                'OR:Profile.fullname:LIKE' => "%{$query}%",
                'OR:Profile.email:LIKE' => "%{$query}%",
            ));
        }

        return $c;
    }


    /**
     * @param xPDOQuery $c
     *
     * @return xPDOQuery
     */
    public function prepareQueryAfterCount(xPDOQuery $c)
    {
        $c->select($this->modx->getSelectColumns('modUser', 'modUser'));
        $c->select($this->modx->getSelectColumns('modUserProfile', 'Profile', '', array('fullname', 'email')));

        return $c;
    }

}

return 'msUserGetListProcessor';