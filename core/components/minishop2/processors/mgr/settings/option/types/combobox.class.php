<?php

class msComboboxType extends msOptionType implements msOptionTypeInterface {

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
/*
 * store: new Ext.data.SimpleStore({
            fields: ['d','v']
            ,data: [[_('yes'),true],[_('no'),false]]
        })

        ,triggerAction: 'all'
        ,editable: false
        ,selectOnFocus: false
        ,preventRender: true
        ,forceSelection: true
        ,enableKeyEvents: true
 */

    public static function getProperties(& $modx) {
        return $modx->miniShop2->config['jsUrl'].'mgr/settings/types/combobox.grid.js';
    }

}

return 'msComboboxType';
