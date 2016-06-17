<?php

require_once MODX_CORE_PATH . 'components/minishop2/processors/mgr/category/getnodes.class.php';

class msOptionCategoryGetNodesProcessor extends msCategoryGetNodesProcessor
{
    protected $categories = array();


    /**
     * @return bool
     */
    public function initialize()
    {
        if ($categories = $this->getProperty('categories')) {
            $this->categories = json_decode($categories, true);
        } elseif ($options = $this->getProperty('options')) {
            $options = json_decode($options, true);
            if (is_array($options) && count($options) === 1) {
                /** @var msOption $option */
                if ($option = $this->modx->getObject('msOption', $options[0])) {
                    $categories = $option->getMany('OptionCategories');
                    $tmp = array();
                    /** @var msCategoryOption $cat */
                    foreach ($categories as $cat) {
                        $category = $cat->getOne('Category');
                        if ($category) {
                            $tmp[] = $category->get('id');
                        }
                    }
                    $this->categories = $tmp;
                }
            }
        }

        return parent::initialize();
    }


    /**
     * @param modResource $resource
     *
     * @return array
     */
    public function prepareResourceNode(modResource $resource)
    {
        $node = parent::prepareResourceNode($resource);
        if (in_array($node['pk'], $this->categories)) {
            $node['checked'] = true;
        }

        return $node;
    }


}

return 'msOptionCategoryGetNodesProcessor';