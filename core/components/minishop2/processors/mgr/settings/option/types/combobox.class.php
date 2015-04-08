<?php

class msComboboxType extends msOptionType implements msOptionTypeInterface {

    public static $script = 'combobox.grid.js';
    public static $xtype = 'minishop2-grid-combobox-options';

    public function getValue($criteria) {
        /** @var msProductOption $value */
        $values = $this->xpdo->getIterator('msProductOption', $criteria);
        $result = array();
        foreach ($values as $value) {
            $result[] = $value->get('value');
        }
        return $result;
    }

    public function setValue($criteria, $value) {

    }

    public function getField($field) {
        if (isset($field['properties']['values'])) {
            $values = $this->xpdo->toJSON(array_chunk($field['properties']['values'],1));
        } else {
            $values = '[]';
        }
        return "{xtype:'modx-combo'
            ,store: new Ext.data.SimpleStore({
                fields: ['value']
                ,data: {$values}
            })
            ,fields: ['value']
            ,displayField: 'value'
            ,valueField: 'value'
            ,mode: 'local'
        }";
    }

}

return 'msComboboxType';
