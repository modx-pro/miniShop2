<?php

class msNumberfieldType extends msOptionType
{

    /**
     * @param $field
     *
     * @return string
     */
    public function getField($field)
    {
        return "{xtype:'numberfield'}";
    }
}

return 'msNumberfieldType';