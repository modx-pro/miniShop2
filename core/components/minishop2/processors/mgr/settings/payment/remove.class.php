<?php

class msPaymentRemoveProcessor extends modObjectRemoveProcessor
{
    public $classKey = 'msPayment';
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

}

return 'msPaymentRemoveProcessor';