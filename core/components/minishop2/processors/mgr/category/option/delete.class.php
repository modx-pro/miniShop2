<?php

class msCategoryOptionDeleteProcessor extends modObjectRemoveProcessor
{
    public $classKey = 'msCategoryOption';
    public $objectType = 'ms2_option';
    public $languageTopics = array('minishop2:default');

    /**
     * @return bool|null|string
     */
    public function initialize()
    {
        $this->object = $this->modx->getObject($this->classKey, array(
            'option_id' => $this->getProperty('option_id'),
            'category_id' => $this->getProperty('category_id'),
        ));
        if (empty($this->object)) {
            return $this->modx->lexicon('ms2_option_err_nfs');
        }

        return true;
    }

    /**
     * @return bool
     */
    public function afterRemove()
    {
        $sql = "UPDATE {$this->modx->getTableName($this->classKey)} SET `rank`=`rank`-1
            WHERE `rank`>{$this->object->get('rank')} AND `category_id`={$this->object->get('category_id')}";
        $this->modx->exec($sql);

        return parent::afterRemove();
    }
}

return 'msCategoryOptionDeleteProcessor';
