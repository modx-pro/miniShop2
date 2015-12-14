<?php

class msOptionGetTypesProcessor extends modObjectGetListProcessor {

    public $languageTopics = array('minishop2:manager');
    /**
     * Get the data of the query
     * @return array
     */
    public function getData() {
        /** @var miniShop2 $ms */
        $ms = $this->modx->getService('minishop2');
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
                    'caption' => $this->modx->lexicon('ms2_ft_'.$name),
                    'xtype' => $className::$xtype
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
     * Prepare the row for iteration
     * @param array $object
     * @return array
     */
    public function prepareRow(array $object) {
        return $object;
    }
}

return 'msOptionGetTypesProcessor';
