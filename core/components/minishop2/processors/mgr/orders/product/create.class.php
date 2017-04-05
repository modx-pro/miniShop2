<?php

class msOrderProductCreateProcessor extends modObjectCreateProcessor
{
    public $classKey = 'msOrderProduct';
    public $languageTopics = array('minishop2');
    public $beforeSaveEvent = 'msOnBeforeCreateOrderProduct';
    public $afterSaveEvent = 'msOnCreateOrderProduct';
    public $permission = 'msorder_save';
    /** @var msOrder $order */
    protected $order;


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
     * @return bool|null|string
     */
    public function beforeSet()
    {
        $count = $this->getProperty('count');
        if ($count <= 0) {
            $this->modx->error->addField('count', $this->modx->lexicon('ms2_err_ns'));
        }

        if ($options = $this->getProperty('options')) {
            $tmp = json_decode($options, true);
            if (!is_array($tmp)) {
                $this->modx->error->addField('options', $this->modx->lexicon('ms2_err_json'));
            } else {
                $this->setProperty('options', $tmp);
            }
        }

        if (!$this->order = $this->modx->getObject('msOrder', $this->getProperty('order_id'))) {
            return $this->modx->lexicon('ms2_err_order_nf');
        }

        /** @var msOrderStatus $status */
        if ($status = $this->order->getOne('Status')) {
            if ($status->get('final')) {
                return $this->modx->lexicon('ms2_err_status_final');
            }
        }

        $this->setProperty('cost', $this->getProperty('price') * $this->getProperty('count'));
        $this->setProperty('product_id', $this->getProperty('id'));

        return !$this->hasErrors();
    }


    /**
     * @return bool
     */
    public function beforeSave()
    {
        $this->object->fromArray(array(
            'rank' => $this->modx->getCount('msOrderProduct'),
        ));

        return parent::beforeSave();
    }

}

return 'msOrderProductCreateProcessor';