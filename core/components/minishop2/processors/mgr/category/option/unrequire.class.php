<?php

require_once(dirname(__FILE__) . '/update.class.php');

class msCategoryOptionUnRequireProcessor extends msCategoryOptionUpdateProcessor
{

    /**
     * @return bool
     */
    public function beforeSet()
    {
        $this->properties = array(
            'required' => false,
        );

        return true;
    }

}

return 'msCategoryOptionUnRequireProcessor';
