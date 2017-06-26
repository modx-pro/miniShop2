<?php

class msCheckboxType extends msOptionType
{

    /**
     * @param $field
     *
     * @return string
     */
    public function getField($field)
    {
        return "{
            xtype:'xcheckbox',
            fieldLabel: null,
            boxLabel: '" . $field['caption'] . "',
            checked: ".(int)$field['value'].",
            convertValue: function (v) {
                return (
                    v === '1' || v === true || v === 'true' ||
                    v === this.submitOnValue || String(v).toLowerCase() === 'on'
                );
            }
        }";
    }
}

return 'msCheckboxType';