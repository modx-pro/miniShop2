<?php

class msOptionCategoryUpdateProcessor extends modObjectUpdateProcessor {
    public $classKey = 'msCategoryOption';
    public $objectType = 'ms2_option';
    public $languageTopics = array('minishop2:default');

    public function initialize() {
        $optionId = $this->getProperty('id');
        if (empty($optionId)) {
            return $this->modx->lexicon($this->objectType.'_err_ns');
        }

        $categoryId = $this->getProperty('category_id');
        if (empty($categoryId)) {
            return $this->modx->lexicon('ms2_category_err_ns');
        }

        $this->object = $this->modx->getObject($this->classKey,array('option_id' => $optionId, 'category_id' => $categoryId));
        if (empty($this->object)) return $this->modx->lexicon($this->objectType.'_err_nfs',array($this->primaryKeyField => array($optionId, $categoryId)));

        if ($this->checkSavePermission && $this->object instanceof modAccessibleObject && !$this->object->checkPolicy('save')) {
            return $this->modx->lexicon('access_denied');
        }

        return true;
    }

    /** {inheritDoc} */
    public function beforeSet() {
        $this->setCheckbox('active');
        $this->setCheckbox('required');

        return parent::beforeSet();
    }

}

return 'msOptionCategoryUpdateProcessor';
