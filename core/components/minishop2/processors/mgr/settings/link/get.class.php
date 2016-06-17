<?php

class msLinkGetProcessor extends modObjectGetProcessor
{
    /** @var msLink $link */
    public $object;
    public $classKey = 'msLink';
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

return 'msLinkGetProcessor';