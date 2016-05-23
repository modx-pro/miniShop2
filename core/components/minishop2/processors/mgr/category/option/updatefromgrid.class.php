<?php

require_once(dirname(__FILE__) . '/update.class.php');

class msCategoryOptionUpdateFromGridProcessor extends msCategoryOptionUpdateProcessor
{

    /**
     * @param modX $modx
     * @param string $className
     * @param array $properties
     *
     * @return modProcessor
     */
    public static function getInstance(modX &$modx, $className, $properties = array())
    {

        /** @var modProcessor $processor */
        $processor = new msCategoryOptionUpdateFromGridProcessor($modx, $properties);

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

        $this->setProperties($data);
        $this->unsetProperty('data');

        return parent::initialize();
    }

}

return 'msCategoryOptionUpdateFromGridProcessor';
