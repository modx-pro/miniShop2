<?php

class msOptionAssignProcessor extends modObjectCreateProcessor
{
    /** @var msCategoryOption $object */
    public $object;
    public $classKey = 'msCategoryOption';
    public $objectType = 'ms2_option';
    public $languageTopics = array('minishop2:default');
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
     * @return bool|null|string
     */
    public function beforeSet()
    {
        $option_id = $this->getProperty('option_id');
        $category_id = $this->getProperty('category_id');

        if (!$option_id || !$this->modx->getCount('msOption', $option_id)) {
            return $this->modx->lexicon('msOption_err_ns');
        } elseif (!$category_id || !$this->modx->getCount('msCategory', $category_id)) {
            return $this->modx->lexicon('msCategoryOption_err_ns');
        }

        $key = array(
            'option_id' => $option_id,
            'category_id' => $category_id,
            'active' => true,
        );
        if (!$this->modx->getCount($this->classKey, $key)) {
            $key['rank'] = $this->modx->getCount($this->classKey, array('category_id' => $category_id));
            $this->object->fromArray($key, '', true, true);
        } else {
            return $this->modx->lexicon($this->objectType . '_err_ae', $key);
        }

        return true;
    }

}

return 'msOptionAssignProcessor';
