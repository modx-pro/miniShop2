<?php

class msTextfieldType extends msOptionType
{

    /**
     * @param $field
     *
     * @return string
     */
    public function getField($field)
    {
        return "{xtype:'textfield'}";
    }

}

return 'msTextfieldType';