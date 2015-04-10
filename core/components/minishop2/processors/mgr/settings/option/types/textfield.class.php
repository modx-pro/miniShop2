<?php

class msTextfieldType extends msOptionType {

    public function getField($field) {
        return "{xtype:'textfield'}";
    }

}

return 'msTextfieldType';