<?php

class msOptionUpdateProcessor extends modObjectUpdateProcessor {
    public $classKey = 'msOption';
    public $objectType = 'ms2_option';
    public $languageTopics = array('minishop2:default');
    /** @var  msOption */
    public $object;
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

    /**
     * @return array|boolean
     */
    public function getCategories() {
        $categories = $this->getProperty('categories', false);
        if ($categories) {
            $categories = $this->modx->fromJSON($categories);
        }
        return $categories;
    }

    public function removeNotAssignedCategories($assignedCats) {
        $q = $this->modx->newQuery('msCategoryOption');
        $q->command('DELETE');
        $q->where(array('option_id' => $this->object->get('id')));
        if (!empty($assignedCats)) {
            $q->where(array('category_id:NOT IN' => $assignedCats));
        }
        $q->prepare();
        $q->stmt->execute();
    }

    public function updateOldKeys() {
        if ($this->oldKey) {
            $q = $this->modx->newQuery('msProductOption');
            $q->command('UPDATE');
            $q->where(array('key' => $this->oldKey));
            $q->set(array('key' => $this->object->get('key')));
            $q->prepare();
            $q->stmt->execute();
        }
    }

    public function updateAssignedCategory() {
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
    }

    public function afterSave() {
        $categories = $this->getCategories();
        if (is_array($categories)) {
            if (!empty($categories)) {
                $categories = $this->object->setCategories($categories);
            }
            // удаляем категории, которые не были установлены
            $this->removeNotAssignedCategories($categories);
            $this->object->set('categories', $categories);
        }

        $this->updateAssignedCategory();

        $this->updateOldKeys();

        return parent::afterSave();
    }
}

return 'msOptionUpdateProcessor';
