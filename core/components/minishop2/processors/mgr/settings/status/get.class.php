<?php

class msOrderStatusGetProcessor extends modObjectGetProcessor
{
    /** @var msOrderStatus $object */
    public $object;
    public $classKey = 'msOrderStatus';
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

return 'msOrderStatusGetProcessor';