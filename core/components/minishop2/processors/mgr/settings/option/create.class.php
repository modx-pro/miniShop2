<?php

class msOptionCreateProcessor extends modObjectCreateProcessor
{
    /** @var msOption $object */
    public $object;
    public $classKey = 'msOption';
    public $objectType = 'ms2_option';
    public $languageTopics = ['minishop2:default'];
    public $permission = 'mssetting_save';

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

        if ($this->doesAlreadyExist(['key' => $key])) {
            $this->addFieldError('key', $this->modx->lexicon($this->objectType . '_err_ae', ['key' => $key]));
        }
        $this->setProperty('key', $key);

        return parent::beforeSet();
    }

    /**
     * @return bool
     */
    public function afterSave()
    {
        if ($categories = json_decode($this->getProperty('categories', false), true)) {
            $enabled = [];
            foreach ($categories as $id => $checked) {
                if ($checked) {
                    $enabled[] = $id;
                }
            }
            if ($enabled) {
                $categories = $this->object->setCategories($enabled);
                $this->object->set('categories', $categories);
            }
        }

        return parent::afterSave();
    }
}

return 'msOptionCreateProcessor';
