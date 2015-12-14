<?php

class msTextareaType extends msOptionType {

    public function getField($field) {
        return "{xtype:'textarea'}";
    }
}

return 'msTextareaType';