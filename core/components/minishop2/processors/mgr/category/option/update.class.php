<?php

class msCategoryOptionUpdateProcessor extends modObjectUpdateProcessor
{
    public $classKey = 'msCategoryOption';
    public $languageTopics = ['minishop2:default'];
    public $permission = 'mscategory_save';

    /**
     * @return bool|null|string
     */
    public function initialize()
    {
        $this->object = $this->modx->getObject($this->classKey, [
            'option_id' => $this->getProperty('option_id'),
            'category_id' => $this->getProperty('category_id'),
        ]);
        if (empty($this->object)) {
            return $this->modx->lexicon('ms2_option_err_nfs');
        }

        return true;
    }

    /**
     * @return bool
     */
    public function beforeSet()
    {
        $this->setCheckbox('active');
        $this->setCheckbox('required');

        return true;
    }
}

return 'msCategoryOptionUpdateProcessor';
