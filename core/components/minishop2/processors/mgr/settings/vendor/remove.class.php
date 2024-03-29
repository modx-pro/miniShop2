<?php

class msVendorRemoveProcessor extends modObjectRemoveProcessor
{
    /** @var msVendor $object */
    public $object;
    public $classKey = 'msVendor';
    public $languageTopics = ['minishop2'];
    public $permission = 'mssetting_save';
    public $beforeRemoveEvent = 'msOnBeforeVendorDelete';
    public $afterRemoveEvent = 'msOnAfterVendorDelete';

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

}

return 'msVendorRemoveProcessor';
