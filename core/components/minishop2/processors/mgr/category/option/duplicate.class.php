<?php

class msCategoryOptionDuplicateProcessor extends modObjectProcessor
{
    public $classKey = 'msCategory';
    public $languageTopics = array('minishop2:default');
    /** @var msCategory $to_object */
    public $to_object;


    /**
     * @return bool|null|string
     */
    public function initialize()
    {
        $from = (int)$this->getProperty('category_from');
        $to = (int)$this->getProperty('category_to');
        if (!$from || !$to) {
            return $this->modx->lexicon('ms2_category_err_ns');
        }
        $this->object = $this->modx->getObject($this->classKey, $from);
        $this->to_object = $this->modx->getObject($this->classKey, $to);

        if (!$this->object || !$this->to_object) {
            return $this->modx->lexicon('ms2_category_err_nfs', array(
                $this->primaryKeyField => array($from, $to),
            ));
        }

        return true;
    }


    /**
     * @return array|string
     */
    public function process()
    {
        $options = $this->object->getMany('CategoryOptions');
        /** @var msCategoryOption $option */
        foreach ($options as $option) {
            $new = $this->modx->getObject('msCategoryOption', array(
                'option_id' => $option->get('option_id'),
                'category_id' => $this->to_object->get('id'),
            ));
            if (!$new) {
                /** @var msCategoryOption $new */
                $new = $this->modx->newObject('msCategoryOption');
                $new->fromArray($option->toArray(), '', true);
            }
            $this->to_object->addMany($new);
        }

        if (!$this->to_object->save()) {
            return $this->failure($this->modx->lexicon('ms2_category_err_save'));
        }

        return $this->cleanup();
    }


    /**
     * @return array|string
     */
    public function cleanup()
    {
        $fields = array();
        if ($options = $this->to_object->getMany('CategoryOptions')) {
            /** @var msCategoryOption $option */
            foreach ($options as $option) {
                $fields[] = $option->get('option_id');
            }
        }
        $this->to_object->set('options', $fields);

        return $this->success('', $this->to_object);
    }

}

return 'msCategoryOptionDuplicateProcessor';