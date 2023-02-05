<?php

class msDatefieldType extends msOptionType
{

    /**
     * @param $field
     *
     * @return string
     */
    public function getField($field)
    {
        return "{
            xtype: 'xdatetime',
            dateFormat: MODx.config.manager_date_format,
            hiddenFormat: MODx.config.manager_date_format,
            startDay: parseInt(MODx.config.manager_week_start),
            hideTime: true,
            timeWidth: 0,
            ctCls: 'x-no-time',
            dateConfig: {
                allowBlank: " . ($field['required'] ? 0 : 1) . ",
            },
            getValue: function() {
                var v = this.dateValue ? new Date(this.dateValue) : '';
                if (v) {
                    v = v.format(this.hiddenFormat);
                }
                return v;
            },
        }";
    }
}

return 'msDatefieldType';
