<?php

class msDeliveryMemberCreateProcessor extends modObjectCreateProcessor
{
    /** @var msPayment $object */
    public $object;
    public $classKey = 'msDeliveryMember';
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
    public function beforeSave()
    {
        $this->object->fromArray($this->getProperties(), '', true, true);

        return true;
    }

}

return 'msDeliveryMemberCreateProcessor';
