<?php

class msProductGetOptionsProcessor extends modObjectProcessor
{
    public $classKey = 'msProductOption';


    public function process()
    {
        $query = trim($this->getProperty('query'));
        $limit = trim($this->getProperty('limit', 10));
        $key = preg_replace('#^options-(.*?)#', '$1', $this->getProperty('key'));

        $c = $this->modx->newQuery('msProductOption');
        $c->sortby('value', 'ASC');
        $c->select('value');
        $c->groupby('value');
        $c->where(array('key' => $key));
        $c->limit($limit);
        if (!empty($query)) {
            $c->where(array('value:LIKE' => "%{$query}%"));
        }
        $found = false;
        if ($c->prepare() && $c->stmt->execute()) {
            $res = $c->stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($res as $v) {
                if ($v['value'] == $query) {
                    $found = true;
                }
            }
        } else {
            $res = array();
        }

        if (!$found && !empty($query)) {
            $res = array_merge_recursive(array(array('value' => $query)), $res);
        }

        return $this->outputArray($res);
    }

}

return 'msProductGetOptionsProcessor';