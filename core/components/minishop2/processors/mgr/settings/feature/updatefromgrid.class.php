<?php

require_once (dirname(__FILE__).'/update.class.php');

class msFeatureUpdateFromGridProcessor extends msFeatureUpdateProcessor {
    public $classKey = 'msFeature';
    public $objectType = 'ms2_feature';
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

return 'msFeatureUpdateFromGridProcessor';
