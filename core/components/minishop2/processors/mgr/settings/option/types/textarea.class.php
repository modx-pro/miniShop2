<?php

class msTextareaType extends msOptionType
{

    /**
     * @param $field
     *
     * @return string
     */
    public function getField($field)
    {
        return "{xtype:'textarea'}";
    }
}

return 'msTextareaType';