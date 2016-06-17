<?php

include_once dirname(__FILE__) . '/combobox.class.php';

class msComboMultipleType extends msComboboxType
{

    /**
     * @param $field
     *
     * @return string
     */
    public function getField($field)
    {
        if (isset($field['properties']['values'])) {
            $values = json_encode(array_chunk($field['properties']['values'], 1));
        } else {
            $values = '[]';
        }

        return "{
            xtype: 'minishop2-combo-options',
            allowAddNewData: false,
            mode:'local',
            store: new Ext.data.SimpleStore({
                fields: ['value'],
                data: {$values}
            })}";
    }


    /**
     * @param $criteria
     *
     * @return array
     */
    public function getValue($criteria)
    {
        /** @var msProductOption $value */
        $values = $this->xpdo->getIterator('msProductOption', $criteria);
        $result = array();
        foreach ($values as $value) {
            $result[] = array('value' => $value->get('value'));
        }

        return $result;
    }

}

return 'msComboMultipleType';