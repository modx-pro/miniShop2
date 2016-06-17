<?php

class msVendorCreateProcessor extends modObjectCreateProcessor
{
    /** @var msVendor $object */
    public $object;
    public $classKey = 'msVendor';
    public $languageTopics = array('minishop2');
    public $permission = 'mssetting_save';


    /**
     * @return bool|null|string
     */
    public function initialize()
    {
        if (!$this->modx->hasPermission($this->permission)) {
            return $this->modx->lexicon('access_denied');
        }

        return parent::initialize();
    }


    /**
     * @return bool
     */
    public function beforeSet()
    {
        $required = array('name');
        foreach ($required as $field) {
            if (!$tmp = trim($this->getProperty($field))) {
                $this->addFieldError($field, $this->modx->lexicon('field_required'));
            } else {
                $this->setProperty($field, $tmp);
            }
        }
        if ($this->modx->getCount($this->classKey, array('name' => $this->getProperty('name')))) {
            $this->modx->error->addField('name', $this->modx->lexicon('ms2_err_ae'));
        }

        return !$this->hasErrors();
    }

}

return 'msVendorCreateProcessor';