<?php

class msCategoryOptionDeleteProcessor extends modObjectRemoveProcessor {
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

        if ($this->checkRemovePermission && $this->object instanceof modAccessibleObject && !$this->object->checkPolicy('remove')) {
            return $this->modx->lexicon('access_denied');
        }

        return true;
    }

    public function afterRemove() {
        $sql = "UPDATE {$this->modx->getTableName($this->classKey)} SET `rank`=`rank`-1
            WHERE `rank`>{$this->object->get('rank')} AND `category_id`={$this->object->get('category_id')}";
        $this->modx->exec($sql);

        return parent::afterRemove();
    }
}

return 'msCategoryOptionDeleteProcessor';
