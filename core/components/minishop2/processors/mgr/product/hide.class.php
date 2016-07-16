<?php

class msProductHideInTreeProcessor extends modObjectUpdateProcessor
{
    public $classKey = 'msProduct';


    /**
     * @return bool
     */
    public function beforeSet()
    {
        $this->properties = array(
            'show_in_tree' => false,
        );

        return true;
    }

}

return 'msProductHideInTreeProcessor';