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

    public function getField() {
        return "{xtype:'modx-combo'}";
    }

}

return 'msComboboxType';