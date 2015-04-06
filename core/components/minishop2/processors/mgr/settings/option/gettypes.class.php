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
        $this->modx->loadClass('msOption', $ms->config['modelPath'].'minishop2/');

        $data = array();
        $limit = intval($this->getProperty('limit'));
        $start = intval($this->getProperty('start'));

        $typeDir = dirname(__FILE__) . '/types';
        $files = scandir($typeDir);
        $data['results'] = array();

        foreach ($files as $file) {
            if (preg_match('/.*?\.class\.php$/i', $file)) {

                $name = str_replace('.class.php', '', $file);
                $className = $ms->loadOptionType($name);
                $properties = null;
                if (class_exists($className)) {
                    /** @var msOptionType|msOptionTypeInterface $optionType */
                    $properties = $className::getProperties($this->modx);
                }

                $data['results'][] = array(
                    'name' => $name,
                    'caption' => $this->modx->lexicon('ms2_ft_'.$name),
                    'properties' => $properties
                );
            }
        }

        $data['total'] = count($data['results']);
        if ($limit > 0) {
            $data['results'] = array_slice($data['results'], $start, $limit);
        }

        return $data;
    }

    public function getType($type) {
        if (array_key_exists($typePath, $this->optionTypes)) {
            $className = $this->optionTypes[$typePath];
        } else {
            $className = include $typePath;
            $this->optionTypes[$typePath] = $className;
        }

        if (class_exists($className)) {
            return new $className($option);
        } else {
            $this->modx->log(modX::LOG_LEVEL_ERROR, 'Could not initialize miniShop2 option type class: "'.$className.'"');
            return null;
        }
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
