<?php

class msOrderProductUpdateProcessor extends modObjectUpdateProcessor
{
    public $classKey = 'msOrderProduct';
    public $objectType = 'msOrderProduct';
    public $languageTopics = ['minishop2'];
    public $beforeSaveEvent = 'msOnBeforeUpdateOrderProduct';
    public $afterSaveEvent = 'msOnUpdateOrderProduct';
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
            if (is_array($options)) {
                $options = json_encode($options, JSON_UNESCAPED_UNICODE);
            }

            $tmp = json_decode($options, true);

            $buildedOptions = $this->buildOptions();

            if (!is_array($tmp)) {
                $this->modx->error->addField('options', $this->modx->lexicon('ms2_err_json'));
            } else {
                $tmp = array_merge($tmp, $buildedOptions);
                $this->setProperty('options', $tmp);
            }
        }

        if (!$this->order = $this->object->getOne('Order')) {
            return $this->modx->lexicon('ms2_err_order_nf');
        }

        /** @var msOrderStatus $status */
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
    public function afterSave()
    {
        // Fix "cache"
        if ($this->order = $this->modx->getObject('msOrder', $this->order->id, false)) {
            $this->order->updateProducts();
        }
    }

    private function buildOptions()
    {
        $options = [];
        foreach ($this->getProperties() as $key => $value) {
            $tmp = explode('-', $key);
            if (is_array($tmp) && count($tmp) === 2 && $tmp[0] === 'option') {
                $options[$tmp[1]] = $value;
            }
        }
        return $options;
    }
}

return 'msOrderProductUpdateProcessor';
