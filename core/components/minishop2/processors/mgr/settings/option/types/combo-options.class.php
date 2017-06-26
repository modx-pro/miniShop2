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
        $c->where(array('value:!=' => ''));
        if ($c->prepare() && $c->stmt->execute()) {
            if (!$result = $c->stmt->fetchAll(PDO::FETCH_ASSOC)) {
                $result = array();
            }
        }

        return $result;
    }

}

return 'msComboOptionsType';
