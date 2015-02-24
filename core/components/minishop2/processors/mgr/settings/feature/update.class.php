<?php

class msFeatureUpdateProcessor extends modObjectUpdateProcessor {
    public $classKey = 'msFeature';
    public $objectType = 'ms2_feature';
    public $languageTopics = array('minishop2:default');

    public function beforeSet() {
        $name = $this->getProperty('name');
        if (empty($name)) {
            $this->addFieldError('name',$this->modx->lexicon($this->objectType.'_err_name_ns'));
        }

        if ($this->doesAlreadyExist(array('name' => $name))) {
            $this->addFieldError('name',$this->modx->lexicon($this->objectType.'_err_ae',array('name' => $name)));
        }

        return parent::beforeSet();
    }
}

return 'msFeatureUpdateProcessor';
