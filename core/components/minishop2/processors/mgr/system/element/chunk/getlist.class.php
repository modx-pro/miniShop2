<?php

class msChunkGetListProcessor extends modObjectGetListProcessor
{
    public $classKey = 'modChunk';
    public $languageTopics = array('chunk');
    public $defaultSortField = 'name';


    /**
     * @param xPDOQuery $c
     *
     * @return xPDOQuery
     */
    public function prepareQueryBeforeCount(xPDOQuery $c)
    {
        if ($id = (int)$this->getProperty('id')) {
            $c->where(array('id' => $id));
        }
        if ($query = trim($this->getProperty('query'))) {
            $c->where(array('name:LIKE' => "%{$query}%"));
        }

        return $c;
    }

}

return 'msChunkGetListProcessor';