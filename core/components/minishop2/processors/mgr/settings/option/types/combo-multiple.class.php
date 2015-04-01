<?php

include_once dirname(__FILE__) . '/combobox.class.php';
class msComboMultipleType extends msComboboxType implements msOptionTypeInterface {


    public function getField() {
        return "{xtype:'minishop2-combo-options'}";
    }

}

return 'msComboMultipleType';