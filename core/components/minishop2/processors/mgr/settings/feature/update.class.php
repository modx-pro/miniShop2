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

        if (($this->object->get('name') != $name) && $this->doesAlreadyExist(array('name' => $name))) {
            $this->addFieldError('name',$this->modx->lexicon($this->objectType.'_err_ae',array('name' => $name)));
        }

        return parent::beforeSet();
    }

    public function getCategories() {
        $categories = $this->getProperty('categories', false);
        if ($categories) {
            $categories = $this->modx->fromJSON($categories);
        }
        $categories = array_map('trim', $categories);

        return $categories;
    }

    public function afterSave() {
        $this->modx->exec("DELETE FROM {$this->modx->getTableName('msCategoryFeature')} WHERE `feature_id` = {$this->object->get('id')};");
        $categories = $this->getCategories();
        $categories = $this->object->setCategories($categories);
        $this->object->set('categories', $categories);

        return parent::afterSave();
    }
}

return 'msFeatureUpdateProcessor';
