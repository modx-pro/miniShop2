<?php

class msProductShowInTreeProcessor extends modObjectUpdateProcessor
{
    public $classKey = 'msProduct';

    /**
     * @return bool
     */
    public function beforeSet()
    {
        $this->properties = [
            'show_in_tree' => true
        ];

        return true;
    }
}

return 'msProductShowInTreeProcessor';
