<?php

class msOrderRemoveProcessor extends modObjectRemoveProcessor
{
    public $classKey = 'msOrder';
    public $languageTopics = ['minishop2'];
    public $permission = 'msorder_remove';

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
