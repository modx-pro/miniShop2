<?php

class msOrderUpdateProcessor extends modObjectUpdateProcessor
{
    public $classKey = 'msOrder';
    public $objectType = 'msOrder';
    public $languageTopics = ['minishop2:default'];
    public $beforeSaveEvent = 'msOnBeforeUpdateOrder';
    public $afterSaveEvent = 'msOnUpdateOrder';
    public $permission = 'msorder_save';
    protected $status;
    protected $delivery;
    protected $payment;
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
     * @return bool|null|string
     */
    public function beforeSet()
    {
        foreach (['status', 'delivery', 'payment'] as $v) {
            if (!$this->$v = $this->getProperty($v)) {
                $this->addFieldError($v, $this->modx->lexicon('ms2_err_ns'));
            }
        }

        if ($status = $this->modx->getObject('msOrderStatus')) {
            if ($status->get('final')) {
                return $this->modx->lexicon('ms2_err_status_final');
            }
        }
        // set "old status"
        $this->setProperty('status', $this->object->get('status'));

        return parent::beforeSet();
    }

    /**
     * @return bool|string
     */
    public function beforeSave()
    {
        $this->object->set('updatedon', time());

        if ($address = $this->object->getOne('Address')) {
            foreach ($this->getProperties() as $k => $v) {
                if (strpos($k, 'addr_') !== false) {
                    $address->set(substr($k, 5), $v);
                }
            }
            $this->object->addOne($address, 'Address');
        }

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

        if ($this->saveObject() == false) {
            return $this->failure($this->modx->lexicon($this->objectType . '_err_save'));
        }

        // set "new status"
        if ($this->object->get('status') != $this->status) {
            $change_status = $this->ms2->changeOrderStatus($this->object->get('id'), $this->status);
            if ($change_status !== true) {
                return $this->failure($change_status);
            }
            $this->object = $this->modx->getObject($this->classKey, $this->object->get('id'), false);
        }

        $this->afterSave();
        $this->fireAfterSaveEvent();
        $this->logManagerAction();

        return $this->cleanup();
    }
}

return 'msOrderUpdateProcessor';
