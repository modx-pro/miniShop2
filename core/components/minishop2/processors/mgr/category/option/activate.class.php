<?php

require_once(dirname(__FILE__) . '/update.class.php');

class msCategoryOptionActivateProcessor extends msCategoryOptionUpdateProcessor
{
    /**
     * @return bool
     */
    public function beforeSet()
    {
        $this->properties = [
            'active' => true,
        ];

        return true;
    }
}

return 'msCategoryOptionActivateProcessor';
