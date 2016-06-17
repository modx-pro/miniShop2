<?php

class msOptionGetTypesProcessor extends modObjectGetListProcessor
{
    public $languageTopics = array('minishop2:manager');


    /**
     * Get the data of the query
     * @return array
     */
    public function getData()
    {
        /** @var miniShop2 $ms */
        $ms = $this->modx->getService('miniShop2');
        $data = array();
        $limit = intval($this->getProperty('limit'));
        $start = intval($this->getProperty('start'));

        $files = $ms->loadOptionTypeList();
        $data['results'] = array();
        foreach ($files as $name) {
            $className = $ms->loadOptionType($name);
            if (class_exists($className)) {
                $data['results'][] = array(
                    'name' => $name,
                    'caption' => $this->modx->lexicon('ms2_ft_' . $name),
                    'xtype' => $className::$xtype,
                );
            }
        }

        $data['total'] = count($data['results']);
        if ($limit > 0) {
            $data['results'] = array_slice($data['results'], $start, $limit);
        }

        return $data;
    }


    /**
     * Iterate across the data
     *
     * @param array $data
     *
     * @return array
     */
    public function iterate(array $data)
    {
        $list = array();
        $list = $this->beforeIteration($list);
        $this->currentIndex = 0;
        /** @var array $array */
        foreach ($data['results'] as $array) {
            $array = $this->prepareArray($array);
            if (!empty($array) && is_array($array)) {
                $list[] = $array;
                $this->currentIndex++;
            }
        }
        $list = $this->afterIteration($list);

        return $list;
    }


    /**
     * Prepare the row for iteration
     *
     * @param array $array
     *
     * @return array
     */
    public function prepareArray($array)
    {
        return $array;
    }
}

return 'msOptionGetTypesProcessor';
