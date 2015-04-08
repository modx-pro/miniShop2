<?php

class msOptionUpdateProcessor extends modObjectUpdateProcessor {
    public $classKey = 'msOption';
    public $objectType = 'ms2_option';
    public $languageTopics = array('minishop2:default');

    protected $oldKey = null;

    public function beforeSet() {
        $key = $this->getProperty('key');
        if (empty($key)) {
            $this->addFieldError('key',$this->modx->lexicon($this->objectType.'_err_name_ns'));
        }

        $oldKey = $this->object->get('key');
        if (($oldKey != $key)) {
            if ($this->doesAlreadyExist(array('key' => $key))) {
                $this->addFieldError('key',$this->modx->lexicon($this->objectType.'_err_ae',array('key' => $key)));
            }

            $this->oldKey = $oldKey;
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

        if (!empty($categories)) {
            $this->modx->exec("DELETE FROM {$this->modx->getTableName('msCategoryOption')} WHERE `option_id` = {$this->object->get('id')};");
            $categories = $this->object->setCategories($categories);
            $this->object->set('categories', $categories);
        }

        $categoryId = $this->getProperty('category_id');
        if ($categoryId) {
            /** @var msCategoryOption $ftCat */
            $ftCat = $this->modx->getObject('msCategoryOption', array(
                'option_id' => $this->object->get('id'),
                'category_id' => $categoryId
            ));

            if ($ftCat) {
                $ftCat->fromArray($this->getProperties());
                $ftCat->save();
            }
        }

        if ($this->oldKey) {
            $sql = "UPDATE {$this->modx->getTableName('msProductOption')} SET `key` = '{$this->object->get('key')}' WHERE `key`='{$this->oldKey}';";
            $this->modx->exec($sql);
        }

        return parent::afterSave();
    }
}

return 'msOptionUpdateProcessor';
