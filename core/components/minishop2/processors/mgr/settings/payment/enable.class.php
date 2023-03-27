<?php

require_once(dirname(__FILE__) . '/update.class.php');

class msPaymentEnableProcessor extends msPaymentUpdateProcessor
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

return 'msPaymentEnableProcessor';
