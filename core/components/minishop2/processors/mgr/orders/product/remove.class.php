<?php

class msOrderProductRemoveProcessor extends modObjectRemoveProcessor
{
    public $classKey = 'msOrderProduct';
    public $languageTopics = array('minishop2');
    public $beforeRemoveEvent = 'msOnBeforeRemoveOrderProduct';
    public $afterRemoveEvent = 'msOnRemoveOrderProduct';
    public $permission = 'msorder_save';
    /** @var msOrder $order */
    protected $order;


    /**
     * @return bool|null|string
     */
    public function beforeRemove()
    {
        if (!$this->order = $this->object->getOne('Order')) {
            return $this->modx->lexicon('ms2_err_order_nf');
        }

        if ($status = $this->order->getOne('Status')) {
            if ($status->get('final')) {
                return $this->modx->lexicon('ms2_err_status_final');
            }
        }

        $this->setProperty('cost', $this->getProperty('price') * $this->getProperty('count'));

        return !$this->hasErrors();
    }


    /**
     * 
     */
    public function afterRemove()
    {
        $this->order->updateProducts();
    }

}

return 'msOrderProductRemoveProcessor';