<?php

require_once(dirname(__FILE__) . '/update.class.php');

class msDeliveryDisableProcessor extends msDeliveryUpdateProcessor
{
    /**
     * @return bool
     */
    public function beforeSet()
    {
        $this->properties = array(
            'active' => false,
        );

        return true;
    }

}

return 'msDeliveryDisableProcessor';
