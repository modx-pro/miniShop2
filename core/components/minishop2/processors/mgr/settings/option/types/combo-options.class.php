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
        $result = array();
        
        $c = $this->xpdo->newQuery('msProductOption', $criteria);
        $c->select('value');
        $c->sortby('value');
        if ($c->prepare() && $c->stmt->execute()) {
            $result =  (array)$c->stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        
        return $result;
    }

}

return 'msComboOptionsType';
