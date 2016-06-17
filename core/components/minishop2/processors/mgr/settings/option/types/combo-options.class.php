<?php

class msComboOptionsType extends msOptionType
{

    /**
     * @param $field
     *
     * @return string
     */
    public function getField($field)
    {
        return "{xtype:'minishop2-combo-options'}";
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

return 'msComboOptionsType';
