<?php

class msContextGetListProcessor extends modObjectGetListProcessor
{
    public $classKey = 'modContext';
    public $languageTopics = array('context');
    public $defaultSortField = 'rank';

    /**
     * @param xPDOQuery $c
     *
     * @return xPDOQuery
     */
    public function prepareQueryBeforeCount(xPDOQuery $c)
    {
        $c->select('key,name');
        if ($key = $this->getProperty('key')) {
            $c->where(array('key' => $key));
        }

        $exclude = $this->getProperty('exclude', 'mgr');
        if (!empty($exclude)) {
            $c->where(array(
                'key:NOT IN' => is_string($exclude) ? explode(',', $exclude) : $exclude,
            ));
        }

        if ($query = trim($this->getProperty('query'))) {
            $c->where(array('name:LIKE' => "%{$query}%"));
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
            $array = array(
                'key'  => $object->get('key'),
                'name' => '(' . $object->get('key') . ') ' . $object->get('name'),
            );
        } else {
            $array = $object->toArray();
        }

        return $array;
    }

}

return 'msContextGetListProcessor';