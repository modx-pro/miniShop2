<?php

class msOrderRemoveProcessor extends modObjectRemoveProcessor
{
    public $classKey = 'msOrder';
    public $languageTopics = array('minishop2');
    public $beforeRemoveEvent = 'msOnBeforeRemoveOrder';
    public $afterRemoveEvent = 'msOnRemoveOrder';
    public $permission = 'msorder_save';


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

return 'msOrderRemoveProcessor';