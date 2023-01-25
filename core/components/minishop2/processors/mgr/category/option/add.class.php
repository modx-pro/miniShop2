<?php

class msCategoryOptionAddProcessor extends modObjectCreateProcessor
{
    public $classKey = 'msCategoryOption';
    public $languageTopics = ['minishop2:default'];
    public $permission = 'mscategory_save';
    /** @var  msCategoryOption */
    public $object;

    /**
     * @return bool|null|string
     */
    public function beforeSet()
    {
        $option = (int)$this->getProperty('option_id');
        $category = (int)$this->getProperty('category_id');
        if (!$option) {
            return $this->modx->lexicon('ms2_option_err_ns');
        } elseif (!$category) {
            return $this->modx->lexicon('ms2_category_err_ns');
        }

        $unique = [
            'option_id' => $option,
            'category_id' => $category,
        ];

        if ($this->doesAlreadyExist($unique)) {
            return $this->modx->lexicon('ms2_option_err_ae', $unique);
        }

        if (!$this->modx->getCount('msOption', $option)) {
            return $this->modx->lexicon('ms2_option_err_nf');
        } elseif (!$this->modx->getCount('msCategory', $category)) {
            return $this->modx->lexicon('ms2_category_err_nf');
        }

        $this->object->set('option_id', $option);
        $this->object->set('category_id', $category);

        $rank = $this->modx->getCount($this->classKey, ['category_id' => $category]);
        $this->object->set('rank', $rank);

        return parent::beforeSet();
    }
}

return 'msCategoryOptionAddProcessor';
