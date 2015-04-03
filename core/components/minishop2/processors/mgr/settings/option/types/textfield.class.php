<?php

class msTextfieldType extends msOptionType implements msOptionTypeInterface {

    public function getValue($criteria) {
        /** @var msProductOption $value */
        $value = $this->xpdo->getObject('msProductOption', $criteria);
        return ($value) ? $value->get('value') : null;
    }

    public function setValue($criteria, $value) {
        /** @var msProductOption $po */
        $po = $this->xpdo->getObject('msProductOption', $criteria);
        // дефолтные значения применяются только к тем товарам, у которых их еще нет
        if (!$po) {
            $po = $this->xpdo->newObject('msProductOption');
            $po->fromArray($criteria);
            $po->set('value', $value);
            $po->save();
        } else if (is_null($po->get('value'))) {
            $po->set('value', $value);
            $po->save();
        }
    }

    public function getField() {
        return "{xtype:'textfield'}";
    }

}

return 'msTextfieldType';