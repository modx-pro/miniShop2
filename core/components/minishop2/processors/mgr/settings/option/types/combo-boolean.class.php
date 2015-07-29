<?php

class msComboBooleanType extends msOptionType {

    public function getField($field) {
        return "{xtype:'modx-combo-boolean'}";
    }

}

return 'msComboBooleanType';