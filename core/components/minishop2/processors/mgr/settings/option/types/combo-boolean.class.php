<?php

class msComboBooleanType extends msOptionType
{

    /**
     * @param $field
     *
     * @return string
     */
    public function getField($field)
    {
        return "{xtype:'modx-combo-boolean'}";
    }

}

return 'msComboBooleanType';