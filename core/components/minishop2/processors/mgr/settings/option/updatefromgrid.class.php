<?php

require_once (dirname(__FILE__).'/update.class.php');

class msOptionUpdateFromGridProcessor extends msOptionUpdateProcessor {
    public $classKey = 'msOption';
    public $objectType = 'ms2_option';
    public $languageTopics = array('minishop2:default');

    public function initialize() {
        $data = $this->getProperty('data');
        if (empty($data)) return $this->modx->lexicon('invalid_data');
        $properties = $this->modx->fromJSON($data);
        $this->setProperties($properties);
        $this->unsetProperty('data');
        return parent::initialize();
    }
}

return 'msOptionUpdateFromGridProcessor';
