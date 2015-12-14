<?php

class msOptionAddProcessor extends modObjectCreateProcessor {
    public $classKey = 'msCategoryOption';
    public $objectType = 'ms2_option';
    public $languageTopics = array('minishop2:default');

    /** @var  msCategoryOption */
    public $object;
    /** @var  msOption */
    public $option;

    public function beforeSet() {
        $optionId = $this->getProperty('option_id');
        if (empty($optionId)) {
            return $this->modx->lexicon($this->objectType.'_err_ns');
        }

        $categoryId = $this->getProperty('category_id');
        if (empty($categoryId)) {
            return $this->modx->lexicon('ms2_category_err_ns');
        }

        $unique = array(
            'option_id' => $optionId,
            'category_id' => $categoryId,
        );

        if ($this->doesAlreadyExist($unique)) {
            return $this->modx->lexicon($this->objectType.'_err_ae', $unique);
        }

        $this->option = $this->modx->getObject('msOption', $optionId);
        if (!$this->option) {
            return $this->modx->lexicon($this->objectType.'_err_nf');
        }

        $category = $this->modx->getObject('msCategory', $categoryId);
        if (!$category) {
            return $this->modx->lexicon('ms2_category_err_nf');
        }

        $this->object->set('option_id', $optionId);
        $this->object->set('category_id', $categoryId);

        $rank = $this->modx->getCount($this->classKey, array('category_id' => $categoryId));
        $this->object->set('rank', $rank);

        return parent::beforeSet();
    }

    public function afterSave() {


        return true;
    }

}

return 'msOptionAddProcessor';
