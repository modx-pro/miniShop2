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
        return "{
            xtype: 'modx-combo-boolean',
            value: " . (filter_var($field['value'], FILTER_VALIDATE_BOOLEAN) ? 1 : 0) . ",
            store: new Ext.data.SimpleStore({
                fields: ['d','v'],
                data: [[_('yes'), 1],[_('no'), 0]]
            })
        }";
    }
}

return 'msComboBooleanType';
