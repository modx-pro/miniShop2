<?php

class msProductGetOptionsProcessor extends modObjectProcessor
{
    public $classKey = 'msProductOption';

    public function process()
    {
        $query = trim($this->getProperty('query'));
        $start = (int)$this->getProperty('start', 0);
        $limit = (int)$this->getProperty('limit', 10);
        $key = preg_replace('#^options-(.*?)#', '$1', $this->getProperty('key'));
        $values = [];

        $c = $this->modx->newQuery('msProductOption');
        $c->sortby('value', 'ASC');
        $c->select('value');
        $c->groupby('value');
        $c->where(['key' => $key]);
        $c->limit(0);
        if (!empty($query)) {
            $c->where(['value:LIKE' => "%{$query}%"]);
        }
        if ($c->prepare() && $c->stmt->execute()) {
            if ($tmp = $c->stmt->fetchAll(PDO::FETCH_COLUMN)) {
                $values = $tmp;
            }
        }

        if ($exclude = json_decode($this->getProperty('exclude'), true)) {
            $values = array_diff($values, $exclude);
        }

        $values = $this->prepareValues($values, $query);
        $count = count($values);
        $values = array_slice($values, $start, $limit);

        return $this->outputArray($values, $count);
    }

    public function prepareValues($values, $query = '')
    {
        if ($words = array_diff(array_map('trim', explode('|', $query)), [''])) {
            $search = [];
            foreach ($words as $word) {
                $s = preg_quote($word, '\\');
                $found = preg_grep("!{$s}!usi", $values);
                if (is_array($found) && !preg_grep("!^{$s}$!si", $found)) {
                    array_unshift($found, $word);
                }
                $search = $found ? array_merge($search, $found) : $search;
            }
            $values = $search;
        }

        $values = array_keys(array_flip($values));
        $values = array_diff($values, ['']);
        foreach ($values as $id => $value) {
            $values[$id] = ['value' => $value];
        }

        return $values;
    }
}

return 'msProductGetOptionsProcessor';
