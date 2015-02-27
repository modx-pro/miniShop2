<?php

class msFeatureGetTypesProcessor extends modObjectGetListProcessor {
    /**
     * Get the data of the query
     * @return array
     */
    public function getData() {
        $data = array();
        $limit = intval($this->getProperty('limit'));
        $start = intval($this->getProperty('start'));

        $typeDir = dirname(__FILE__) . '/types';
        $files = scandir($typeDir);
        $data['results'] = array();
        foreach ($files as $file) {
            if (preg_match('/.*?\.class\.php$/i', $file)) {
                include_once($typeDir . '/' . $file);
                $name = str_replace('.class.php', '', $file);
                $data['results'][] = array(
                    'name' => $name,
                    'caption' => $this->modx->lexicon('ms_ft_'.$name),
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

return 'msFeatureGetTypesProcessor';
