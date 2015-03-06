<?php

class msFeatureAddProcessor extends modObjectCreateProcessor {
    public $classKey = 'msCategoryFeature';
    public $objectType = 'ms2_feature';
    public $languageTopics = array('minishop2:default');

    public function beforeSet() {
        $featureId = $this->getProperty('feature_id');
        if (empty($featureId)) {
            return $this->modx->lexicon($this->objectType.'_err_ns');
        }

        $categoryId = $this->getProperty('category_id');
        if (empty($categoryId)) {
            return $this->modx->lexicon('ms2_category_err_ns');
        }

        $unique = array(
            'feature_id' => $featureId,
            'category_id' => $categoryId,
        );

        if ($this->doesAlreadyExist($unique)) {
            return $this->modx->lexicon($this->objectType.'_err_ae', $unique);
        }

        $feature = $this->modx->getObject('msFeature', $featureId);
        if (!$feature) {
            return $this->modx->lexicon($this->objectType.'_err_nf');
        }

        $category = $this->modx->getObject('msCategory', $categoryId);
        if (!$category) {
            return $this->modx->lexicon('ms2_category_err_nf');
        }

        return parent::beforeSet();
    }

}

return 'msFeatureAddProcessor';
