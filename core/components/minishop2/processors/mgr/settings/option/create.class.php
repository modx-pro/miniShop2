<?php

class msOptionCreateProcessor extends modObjectCreateProcessor {
    public $classKey = 'msOption';
    public $objectType = 'ms2_option';
    public $languageTopics = array('minishop2:default');
    /** @var msOption */
    public $object;

    public function beforeSet() {
        $key = $this->getProperty('key');
        if (empty($key)) {
            $this->addFieldError('key',$this->modx->lexicon($this->objectType.'_err_name_ns'));
        }

        if ($this->doesAlreadyExist(array('key' => $key))) {
            $this->addFieldError('key',$this->modx->lexicon($this->objectType.'_err_ae',array('key' => $key)));
        }

        return parent::beforeSet();
    }

    public function getCategories() {
        $categories = $this->getProperty('categories', false);
        if ($categories) {
            $categories = $this->modx->fromJSON($categories);
        } else {
            $categories = array();
        }

        return $categories;
    }

    public function afterSave() {
        $categories = $this->getCategories();
        $categories = $this->object->setCategories($categories);
        $this->object->set('categories', $categories);

        return parent::afterSave();
    }
}

return 'msOptionCreateProcessor';
