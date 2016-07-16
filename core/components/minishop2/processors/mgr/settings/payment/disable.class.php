<?php

require_once(dirname(__FILE__) . '/update.class.php');

class msPaymentDisableProcessor extends msPaymentUpdateProcessor
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

return 'msPaymentDisableProcessor';
