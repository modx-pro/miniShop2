<?php

require_once(dirname(__FILE__) . '/update.class.php');

class msCategoryOptionRequireProcessor extends msCategoryOptionUpdateProcessor
{

    /**
     * @return bool
     */
    public function beforeSet()
    {
        $this->properties = [
            'required' => true,
        ];

        return true;
    }
}

return 'msCategoryOptionRequireProcessor';
