<?php

class msVendorGetProcessor extends modObjectGetProcessor
{
    /** @var msVendor $object */
    public $object;
    public $classKey = 'msVendor';
    public $languageTopics = array('minishop2');
    public $permission = 'mssetting_view';


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

return 'msVendorGetProcessor';