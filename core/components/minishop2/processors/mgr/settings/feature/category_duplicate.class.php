<?php

class msFeatureCategoryDuplicateProcessor extends modObjectProcessor {
    public $classKey = 'msCategory';
    public $objectType = 'ms2_category';
    public $languageTopics = array('minishop2:default');
    /** @var null|msCategory */
    public $to_object = null;

    public function initialize() {
        $catFromKey = $this->getProperty('category_from',false);
        $catToKey = $this->getProperty('category_to',false);
        if (!$catFromKey || !$catToKey) return $this->modx->lexicon($this->objectType.'_err_ns');
        $this->object = $this->modx->getObject($this->classKey,$catFromKey);
        $this->to_object = $this->modx->getObject($this->classKey,$catToKey);

        if (empty($this->object) || empty($this->to_object))
            return $this->modx->lexicon($this->objectType.'_err_nfs',array($this->primaryKeyField => array($catFromKey, $catToKey)));

        return true;
    }

    public function cleanup() {
        $cat_fts = $this->to_object->getMany('CategoryFeatures');
        $fts = array();
        foreach ($cat_fts as $cat_ft) {
            $fts[] = $cat_ft->get('feature_id');
        }
        $this->to_object->set('features', $fts);
        return $this->success('', $this->to_object);
    }

    public function process() {
        $cat_fts = $this->object->getMany('CategoryFeatures');
        $this->to_object->addMany($cat_fts);
        if ($this->to_object->save() == false) {
            return $this->failure($this->modx->lexicon($this->objectType.'_err_save'));
        }

        return $this->cleanup();
    }

}

return 'msFeatureCategoryDuplicateProcessor';