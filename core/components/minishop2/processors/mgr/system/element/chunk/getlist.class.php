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
        $query = $this->getProperty('query');
        if (!empty($query)) {
            $c->where(array('name:LIKE' => "%{$query}%"));
        }

        return $c;
    }

}

return 'msChunkGetListProcessor';