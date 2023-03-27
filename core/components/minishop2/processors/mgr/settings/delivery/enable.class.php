<?php

require_once(dirname(__FILE__) . '/update.class.php');

class msDeliveryEnableProcessor extends msDeliveryUpdateProcessor
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

return 'msDeliveryEnableProcessor';
