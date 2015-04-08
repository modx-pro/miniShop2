<?php

include_once dirname(__FILE__) . '/combobox.class.php';
class msComboMultipleType extends msComboboxType implements msOptionTypeInterface {


    public function getField($field) {
        return "{xtype:'minishop2-combo-options'}";
    }

    public function getValue($criteria) {

    }

    public function setValue($criteria, $value) {

    }

}

return 'msComboMultipleType';