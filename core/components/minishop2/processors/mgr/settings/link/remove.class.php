<?php

class msLinkRemoveProcessor extends modObjectRemoveProcessor
{
    /** @var msLink $link */
    public $object;
    public $classKey = 'msLink';
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

return 'msLinkRemoveProcessor';