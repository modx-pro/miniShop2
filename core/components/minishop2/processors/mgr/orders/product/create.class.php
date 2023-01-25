<?php

class msOrderProductCreateProcessor extends modObjectCreateProcessor
{
    public $classKey = 'msOrderProduct';
    public $objectType = 'msOrderProduct';
    public $languageTopics = ['minishop2'];
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

        $this->order = $this->modx->getObject('msOrder', ['id' => $this->getProperty('order_id')]);
        if (!$this->order) {
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
        $this->object->fromArray([
            'rank' => $this->modx->getCount('msOrderProduct'),
        ]);

        return parent::beforeSave();
    }

    /**
     *
     */
    public function afterSave()
    {
        // Fix "cache"
        if ($this->order = $this->modx->getObject('msOrder', $this->order->id, false)) {
            $this->order->updateProducts();
        }
    }
}

return 'msOrderProductCreateProcessor';
