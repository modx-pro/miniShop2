<?php

class msOptionUpdateProcessor extends modObjectUpdateProcessor
{
    /** @var msOption $object */
    public $object;
    public $classKey = 'msOption';
    public $objectType = 'ms2_option';
    public $languageTopics = array('minishop2:default');
    protected $oldKey = null;
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
        $key = $this->getProperty('key');
        if (empty($key)) {
            $this->addFieldError('key', $this->modx->lexicon($this->objectType . '_err_name_ns'));
        }
        $key = str_replace('.', '_', $key);

        $oldKey = $this->object->get('key');
        if (($oldKey != $key)) {
            if ($this->doesAlreadyExist(array('key' => $key))) {
                $this->addFieldError('key', $this->modx->lexicon($this->objectType . '_err_ae', array('key' => $key)));
            }

            $this->oldKey = $oldKey;
        }
        $this->setProperty('key', $key);

        return parent::beforeSet();
    }


    /**
     * @return array|boolean
     */
    public function getCategories()
    {
        $categories = $this->getProperty('categories', false);
        if ($categories) {
            $categories = json_decode($categories, true);
        }

        return $categories;
    }


    /**
     * @param $assignedCats
     */
    public function removeNotAssignedCategories($assignedCats)
    {
        $q = $this->modx->newQuery('msCategoryOption');
        $q->command('DELETE');
        $q->where(array('option_id' => $this->object->get('id')));
        if (!empty($assignedCats)) {
            $q->where(array('category_id:NOT IN' => $assignedCats));
        }
        $q->prepare();
        $q->stmt->execute();
    }


    /**
     *
     */
    public function updateOldKeys()
    {
        if ($this->oldKey) {
            $q = $this->modx->newQuery('msProductOption');
            $q->command('UPDATE');
            $q->where(array('key' => $this->oldKey));
            $q->set(array('key' => $this->object->get('key')));
            $q->prepare();
            $q->stmt->execute();
        }
    }


    /**
     *
     */
    public function updateAssignedCategory()
    {
        $categoryId = $this->getProperty('category_id');
        if ($categoryId) {
            /** @var msCategoryOption $ftCat */
            $ftCat = $this->modx->getObject('msCategoryOption', array(
                'option_id' => $this->object->get('id'),
                'category_id' => $categoryId,
                'active' => true,
            ));

            if ($ftCat) {
                $ftCat->fromArray($this->getProperties());
                $ftCat->save();
            }
        }
    }


    /**
     * @return bool
     */
    public function afterSave()
    {
        $categories = $this->getCategories();
        if (is_array($categories)) {
            if (!empty($categories)) {
                $categories = $this->object->setCategories($categories);
            }
            $this->removeNotAssignedCategories($categories);
            $this->object->set('categories', $categories);
        }
        $this->updateAssignedCategory();
        $this->updateOldKeys();

        return parent::afterSave();
    }
}

return 'msOptionUpdateProcessor';
