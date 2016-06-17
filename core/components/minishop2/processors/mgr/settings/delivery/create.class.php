<?php

class msDeliveryCreateProcessor extends modObjectCreateProcessor
{
    /** @var msDelivery $object */
    public $object;
    public $classKey = 'msDelivery';
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
    public function beforeSet()
    {
        $required = array('name');
        foreach ($required as $field) {
            if (!$tmp = trim($this->getProperty($field))) {
                $this->addFieldError($field, $this->modx->lexicon('field_required'));
            } else {
                $this->setProperty($field, $tmp);
            }
        }
        if ($this->modx->getCount($this->classKey, array('name' => $this->getProperty('name')))) {
            $this->modx->error->addField('name', $this->modx->lexicon('ms2_err_ae'));
        }

        $prices = array('price', 'distance_price', 'weight_price');
        foreach ($prices as $field) {
            if ($tmp = $this->getProperty($field)) {
                $tmp = preg_replace(array('#[^0-9%\-,\.]#', '#,#'), array('', '.'), $tmp);
                if (strpos($tmp, '%') !== false) {
                    $tmp = str_replace('%', '', $tmp) . '%';
                }
                if (strpos($tmp, '-') !== false) {
                    $tmp = str_replace('-', '', $tmp) * -1;
                }
                if (empty($tmp)) {
                    $tmp = 0;
                }
                $this->setProperty($field, $tmp);
            }
        }

        return !$this->hasErrors();
    }


    /**
     * @return bool
     */
    public function beforeSave()
    {
        $this->object->fromArray(array(
            'rank' => $this->modx->getCount($this->classKey),
        ));

        return parent::beforeSave();
    }

}

return 'msDeliveryCreateProcessor';