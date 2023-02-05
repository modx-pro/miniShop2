<?php

class msComboColorsType extends msOptionType
{

    public static $script = 'combobox-colors.grid.js';
    public static $xtype = 'minishop2-grid-combobox-colors';

    /**
     * @param $field
     *
     * @return string
     */
    public function getField($field)
    {
        $kout = [];
        foreach ($field['properties']['values'] as $line) {
            $kout[] = [$line['value'], $line['name']];
        }
        $values = json_encode($kout, 1);
        $tplka = '<tpl for="." ><div class="x-combo-list-item"><span><b>{value}</b> <span style="margin-left:5px;padding:1px 8px;background-color:{name}"></span></span></div></tpl>';

        return "{
            xtype: 'minishop2-combo-options',
            allowAddNewData: false,
            displayField : 'name',
            valueField : 'value',
            displayFieldTpl: '<span style=\"padding:1px 8px;background-color:{name}\" title=\"{value}\"><\/span>',
            pinList: true,
            tpl: new Ext.XTemplate('" . $tplka . "'),
            mode: 'local',
            store: new Ext.data.SimpleStore({
                fields: ['name','value'],
                data: {$values}
            })
        }";
    }

    /**
     * @param $criteria
     *
     * @return array
     */
    public function getValue($criteria)
    {
        $result = array();

        $c = $this->xpdo->newQuery('msProductOption', $criteria);
        $c->select('value');
        $c->where(array('value:!=' => ''));
        if ($c->prepare() && $c->stmt->execute()) {
            if (!$result = $c->stmt->fetchAll(PDO::FETCH_ASSOC)) {
                $result = array();
            }
        }

        return $result;
    }

    /**
     * @param $criteria
     *
     * @return array
     */
    public function getRowValue($criteria)
    {
        $result = array();

        $rows = $this->getValue($criteria);
        foreach ($rows as $row) {
            $result[] = $row['value'];
        }

        return $result;
    }
}

return 'msComboColorsType';
