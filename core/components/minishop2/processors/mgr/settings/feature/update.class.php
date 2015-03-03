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
            $categories = explode(',', $categories);
        }
        $categories = array_map('trim', $categories);

        return $categories;
    }

    public function afterSave() {
        $categories = $this->getCategories();
        foreach ($categories as $category) {
            $catObj = $this->modx->getObject('msCategory', $category);
            if ($catObj) {
                $catFtObj = $this->modx->newObject('msCategoryFeature');
                $catFtObj->set('category_id', $category);
                $this->object->addMany($catFtObj);
            }
        }
        $this->object->save();

        return parent::afterSave();
    }
}

return 'msFeatureUpdateProcessor';
