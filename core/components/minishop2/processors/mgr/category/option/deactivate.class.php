<?php

require_once(dirname(__FILE__) . '/update.class.php');

class msCategoryOptionDeActivateProcessor extends msCategoryOptionUpdateProcessor
{

    /**
     * @return bool
     */
    public function beforeSet()
    {
        $this->properties = [
            'active' => false,
        ];

        return true;
    }
}

return 'msCategoryOptionDeActivateProcessor';
