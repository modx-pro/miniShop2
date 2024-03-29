<?php

class msVendorCreateProcessor extends modObjectCreateProcessor
{
    /** @var msVendor $object */
    public $object;
    public $classKey = 'msVendor';
    public $languageTopics = ['minishop2'];
    public $permission = 'mssetting_save';
    public $beforeSaveEvent = 'msOnBeforeVendorCreate';
    public $afterSaveEvent = 'msOnAfterVendorCreate';

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
    public function beforeSave()
    {
        $this->object->fromArray([
             'rank' => $this->modx->getCount($this->classKey),
         ]);

        return parent::beforeSave();
    }

    /**
     * @return bool
     */
    public function beforeSet()
    {
        $required = ['name'];
        foreach ($required as $field) {
            if (!$tmp = trim($this->getProperty($field))) {
                $this->addFieldError($field, $this->modx->lexicon('field_required'));
            } else {
                $this->setProperty($field, $tmp);
            }
        }
        if ($this->modx->getCount($this->classKey, ['name' => $this->getProperty('name')])) {
            $this->modx->error->addField('name', $this->modx->lexicon('ms2_err_ae'));
        }

        return !$this->hasErrors();
    }

}

return 'msVendorCreateProcessor';
