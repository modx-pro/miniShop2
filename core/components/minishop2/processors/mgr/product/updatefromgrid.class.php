<?php

require_once(dirname(__FILE__) . '/update.class.php');

class msProductUpdateFromGridProcessor extends msProductUpdateProcessor
{

    /**
     * @param modX $modx
     * @param string $className
     * @param array $properties
     *
     * @return modProcessor
     */
    public static function getInstance(modX &$modx, $className, $properties = [])
    {
        /** @var modProcessor $processor */
        $processor = new msProductUpdateFromGridProcessor($modx, $properties);

        return $processor;
    }

    /**
     * @return bool|null|string
     */
    public function initialize()
    {
        $data = $this->getProperty('data');
        if (empty($data)) {
            return $this->modx->lexicon('invalid_data');
        }

        $data = json_decode($data, true);
        if (empty($data)) {
            return $this->modx->lexicon('invalid_data');
        }

        $data = $this->prepareValues($data);
        $this->setProperties($data);
        $this->unsetProperty('data');

        return parent::initialize();
    }

    /**
     * @param array $data
     *
     * @return array
     */
    public function prepareValues(array $data)
    {
        $fields = $this->modx->getFieldMeta('modResource');
        foreach ($fields as $key => $field) {
            if ($field['phptype'] == 'timestamp') {
                if (!empty($data[$key]) && is_numeric($data[$key])) {
                    $data[$key] = date('Y-m-d H:i:s', $data[$key]);
                }
            }
        }

        return $data;
    }

    /**
     * @return array|string
     */
    public function beforeSet()
    {
        $properties = $this->getProperties();
        $options = $this->object->loadData()->get('options');
        foreach ($properties as $key => $value) {
            if (strpos($key, 'options-') === 0) {
                $options[substr($key, 8)] = $value;
                $this->unsetProperty($key);
            }
        }
        if (!empty($options)) {
            $this->setProperty('options', $options);
        }

        return parent::beforeSet();
    }
}

return 'msProductUpdateFromGridProcessor';
