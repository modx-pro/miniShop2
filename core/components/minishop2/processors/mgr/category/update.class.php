<?php

require_once MODX_CORE_PATH . 'model/modx/modprocessor.class.php';
require_once MODX_CORE_PATH . 'model/modx/processors/resource/update.class.php';

class msCategoryUpdateProcessor extends modResourceUpdateProcessor
{
    public $classKey = 'msCategory';
    public $languageTopics = array('resource', 'minishop2:default');
    public $permission = 'mscategory_save';
    public $beforeSaveEvent = 'OnBeforeDocFormSave';
    public $afterSaveEvent = 'OnDocFormSave';


    /**
     * @return bool|null|string
     */
    public function initialize()
    {
        $primaryKey = $this->getProperty($this->primaryKeyField, false);
        if (empty($primaryKey)) {
            return $this->modx->lexicon($this->classKey . '_err_ns');
        }

        if (!$this->modx->getCount($this->classKey, array('id' => $primaryKey, 'class_key' => $this->classKey))) {
            if ($res = $this->modx->getObject('modResource', $primaryKey)) {
                $res->set('class_key', $this->classKey);
                $res->save();
            }
        }

        return parent::initialize();
    }


    /**
     * @return int|mixed|string
     */
    public function checkFriendlyAlias()
    {
        if ($this->workingContext->getOption('ms2_category_id_as_alias')) {
            $alias = $this->object->id;
            $this->setProperty('alias', $alias);
        } else {
            $alias = parent::checkFriendlyAlias();
        }

        return $alias;
    }


    /**
     * @return bool
     */
    public function beforeSave()
    {
        $this->object->set('isfolder', true);

        return parent::beforeSave();
    }
}