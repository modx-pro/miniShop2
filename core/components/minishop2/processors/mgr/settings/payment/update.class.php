<?php

class msPaymentUpdateProcessor extends modObjectUpdateProcessor
{
    /** @var msPayment $object */
    public $object;
    public $classKey = 'msPayment';
    public $languageTopics = ['minishop2'];
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
    public function beforeSet()
    {
        $required = ['name'];
        foreach ($required as $field) {
            if (!$tmp = trim($this->getProperty($field))) {
                $this->addFieldError($field, $this->modx->lexicon('field_required'));
            } else {
                $this->setProperty($field, $tmp);
            }
        }
        $name = $this->getProperty('name');
        if ($this->modx->getCount($this->classKey, ['name' => $name, 'id:!=' => $this->object->id])) {
            $this->modx->error->addField('name', $this->modx->lexicon('ms2_err_ae'));
        }

        $prices = ['price'];
        foreach ($prices as $field) {
            if ($tmp = $this->getProperty($field)) {
                $tmp = $this->preparePrice($tmp);
                $this->setProperty($field, $tmp);
            }
        }

        return !$this->hasErrors();
    }

    public function preparePrice($price = 0)
    {
        $sign = '';
        $price = preg_replace(['#[^0-9%\-,\.]#', '#,#'], ['', '.'], $price);
        if (strpos($price, '-') !== false) {
            $price = str_replace('-', '', $price);
            $sign = '-';
        }
        if (strpos($price, '%') !== false) {
            $price = str_replace('%', '', $price) . '%';
        }
        $price = $sign . $price;
        if (empty($price)) {
            $price = 0;
        }

        return $price;
    }
}

return 'msPaymentUpdateProcessor';
