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
        $categories = $this->modx->getOption('ms2_chunks_categories');
        if (!empty($categories)) {
            $c->where(array(
                'category:IN' => explode(',',$categories)
            ));
        }
        if ($id = (int)$this->getProperty('id')) {
            $c->where(array('id' => $id));
        }
        if ($query = trim($this->getProperty('query'))) {
            $c->where(array(
                'name:LIKE' => "%{$query}%",
                'OR:description:LIKE' => "%{$query}%",
            ));
        }

        return $c;
    }


    /**
    * @param xPDOObject $object
    *
    * @return array
    */
    public function prepareRow(xPDOObject $object)
    {
        $array = $object->toArray();

        if (!empty($array['description'])) {
            $array['name'] = $array['description'];
        }

        return $array;
    }

}

return 'msChunkGetListProcessor';
