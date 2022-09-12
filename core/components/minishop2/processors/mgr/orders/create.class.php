<?php

class msOrderCreateProcessor extends modObjectCreateProcessor
{
    public $classKey = 'msOrder';
    public $objectType = 'msOrder';
    public $languageTopics = array('minishop2:default');
    // events with Mgr suffix for differentiation from frontend orders
    public $beforeSaveEvent = 'msOnBeforeMgrCreateOrder';
    public $afterSaveEvent = 'msOnMgrCreateOrder';
    public $permission = 'msorder_save';
    /** @var  miniShop2 $ms2 */
    protected $ms2;


    /**
    * @return bool|null|string
    */
    public function initialize()
    {
        $this->ms2 = $this->modx->getService('miniShop2');

        if (!$this->modx->hasPermission($this->permission)) {
            return $this->modx->lexicon('access_denied');
        }

        return parent::initialize();
    }

    /**
    * @return bool|string
    */
    public function beforeSave()
    {
        $this->object->set('num', $this->ms2->getNextOrderNum());
        $this->object->set('user_id', $this->modx->user->get('id'));
        $this->object->set('createdon', time());

        return parent::beforeSave();
    }


    /**
    * {@inheritDoc}
    * @return mixed
    */
    public function process()
    {
        /* Run the beforeSet method before setting the fields, and allow stoppage */
        $canSave = $this->beforeSet();
        if ($canSave !== true) {
            return $this->failure($canSave);
        }

        $this->object->fromArray($this->getProperties());

        /* Run the beforeSave method and allow stoppage */
        $canSave = $this->beforeSave();
        if ($canSave !== true) {
            return $this->failure($canSave);
        }

        /* run object validation */
        if (!$this->object->validate()) {
            /** @var modValidator $validator */
            $validator = $this->object->getValidator();
            if ($validator->hasMessages()) {
                foreach ($validator->getMessages() as $message) {
                    $this->addFieldError($message['field'], $this->modx->lexicon($message['message']));
                }
            }
        }

        /* run the before save event and allow stoppage */
        $preventSave = $this->fireBeforeSaveEvent();
        if (!empty($preventSave)) {
            return $this->failure($preventSave);
        }

        if ($this->saveObject() === false) {
            return $this->failure($this->modx->lexicon($this->objectType . '_err_save'));
        }

        // Trying to set status "new". If it fails - the author will handle it on a front
        $this->ms2->changeOrderStatus($this->object->get('id'), 1); // same id in msOrderHandler
        $this->object = $this->modx->getObject($this->classKey, $this->object->get('id'), false);

        $this->afterSave();
        $this->fireAfterSaveEvent();
        $this->logManagerAction();

        return $this->cleanup();
    }

}

return 'msOrderCreateProcessor';
