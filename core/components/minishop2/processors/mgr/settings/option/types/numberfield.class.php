<?php

class msNumberfieldType extends msOptionType {

    public function getField($field) {
        return "{xtype:'numberfield'}";
    }
}

return 'msNumberfieldType';