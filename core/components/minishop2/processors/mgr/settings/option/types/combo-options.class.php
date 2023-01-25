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
        $result = [];

        $c = $this->xpdo->newQuery('msProductOption', $criteria);
        $c->select('value');
        $c->where(['value:!=' => '']);
        if ($c->prepare() && $c->stmt->execute()) {
            if (!$result = $c->stmt->fetchAll(PDO::FETCH_ASSOC)) {
                $result = [];
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
        $result = [];

        $rows = $this->getValue($criteria);
        foreach ($rows as $row) {
            $result[] = $row['value'];
        }

        return $result;
    }
}

return 'msComboOptionsType';
