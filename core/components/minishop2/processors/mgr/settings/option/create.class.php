<?php

class msOptionCreateProcessor extends modObjectCreateProcessor
{
    /** @var msOption $object */
    public $object;
    public $classKey = 'msOption';
    public $objectType = 'ms2_option';
    public $languageTopics = array('minishop2:default');


    /**
     * @return bool
     */
    public function beforeSet()
    {
        $key = $this->getProperty('key');
        if (empty($key)) {
            $this->addFieldError('key', $this->modx->lexicon($this->objectType . '_err_name_ns'));
        }
        $key = str_replace('.', '_', $key);

        if ($this->doesAlreadyExist(array('key' => $key))) {
            $this->addFieldError('key', $this->modx->lexicon($this->objectType . '_err_ae', array('key' => $key)));
        }
        $this->setProperty('key', $key);

        return parent::beforeSet();
    }


    /**
     * @return array|mixed
     */
    public function getCategories()
    {
        $categories = $this->getProperty('categories', false);
        if ($categories) {
            $categories = json_decode($categories, true);
        } else {
            $categories = array();
        }

        return $categories;
    }


    /**
     * @return bool
     */
    public function afterSave()
    {
        $categories = $this->getCategories();
        $categories = $this->object->setCategories($categories);
        $this->object->set('categories', $categories);

        return parent::afterSave();
    }
}

return 'msOptionCreateProcessor';
