<?php

class msResourceGetListProcessor extends modObjectGetListProcessor
{
    public $classKey = 'modResource';
    public $languageTopics = ['resource'];
    public $defaultSortField = 'pagetitle';

    /**
     * @param xPDOQuery $c
     *
     * @return xPDOQuery
     */
    public function prepareQueryBeforeCount(xPDOQuery $c)
    {
        if ($this->getProperty('combo')) {
            $c->select('id,pagetitle');
        }
        if ($id = (int)$this->getProperty('id')) {
            $c->where(['id' => $id]);
        }
        if ($query = trim($this->getProperty('query'))) {
            $c->where(['pagetitle:LIKE' => "%{$query}%"]);
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
        if ($this->getProperty('combo')) {
            $array = [
                'id' => $object->get('id'),
                'pagetitle' => '(' . $object->get('id') . ') ' . $object->get('pagetitle'),
            ];
        } else {
            $array = $object->toArray();
        }

        return $array;
    }
}

return 'msResourceGetListProcessor';
