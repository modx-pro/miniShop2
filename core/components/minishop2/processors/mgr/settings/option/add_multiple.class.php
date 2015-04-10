<?php

class msOptionAddMultipleProcessor extends modObjectProcessor {
    public $classKey = 'msCategoryOption';
    public $objectType = 'ms2_option';
    public $languageTopics = array('minishop2:default');

    /** @var  msCategoryOption */
    public $object;
    /** @var  msOption */
    public $option;

    public function process() {

        $optionIds = $this->getProperty('options');
        if (empty($optionIds)) {
            return $this->modx->lexicon($this->objectType.'_err_ns');
        }

        $categoryIds = $this->getProperty('categories');
        if (empty($categoryIds)) {
            return $this->modx->lexicon('ms2_category_err_ns');
        }

        $options = explode(',',$optionIds);
        $categories = $this->modx->fromJSON($categoryIds);

        $path = dirname(dirname(dirname(dirname(__FILE__)))) . DIRECTORY_SEPARATOR;

        foreach ($options as $option) {
            foreach ($categories as $category) {
                if (!empty($option) && !empty($category)) {
                    $this->modx->runProcessor('mgr/settings/option/add',array(
                        'option_id' => $option,
                        'category_id' => $category,
                    ), array('processors_path' => $path));
                    $this->modx->error->reset();
                }
            }
        }
        return $this->success();
    }

}

return 'msOptionAddMultipleProcessor';
