<?php

class msProductSortProcessor extends modObjectProcessor
{
    public $classKey = 'msProduct';
    private $_parent;


    /**
     * @return array|string
     */
    public function process()
    {
        /** @var msProduct $target */
        if (!$target = $this->modx->getObject($this->classKey, $this->getProperty('target'))) {
            return $this->failure();
        }
        $this->_parent = $target->get('parent');

        $sources = $this->modx->fromJSON($this->getProperty('sources'));
        if (!is_array($sources)) {
            return $this->failure();
        }
        foreach ($sources as $id) {
            /** @var msProduct $source */
            $source = $this->modx->getObject($this->classKey, $id);
            if ($source->get('parent') == $this->_parent) {
                $this->sort($source, $target);
            } else {
                $this->move($source);
            }
        }

        if (!$this->modx->getCount($this->classKey, array('menuindex' => 0, 'parent' => $this->_parent))) {
            $this->updateIndex();
        }

        return $this->modx->error->success();
    }


    /**
     * @param msProduct $source
     * @param msProduct $target
     */
    public function sort(msProduct $source, msProduct $target)
    {
        $c = $this->modx->newQuery('msProduct');
        $c->command('UPDATE');
        $c->where(array(
            'parent' => $this->_parent,
        ));
        if ($source->get('menuindex') < $target->get('menuindex')) {
            $c->query['set']['menuindex'] = array(
                'value' => '`menuindex` - 1',
                'type' => false,
            );
            $c->andCondition(array(
                'menuindex:<=' => $target->menuindex,
                'menuindex:>' => $source->menuindex,
            ));
            $c->andCondition(array(
                'menuindex:>' => 0,
            ));
        } else {
            $c->query['set']['menuindex'] = array(
                'value' => '`menuindex` + 1',
                'type' => false,
            );
            $c->andCondition(array(
                'menuindex:>=' => $target->menuindex,
                'menuindex:<' => $source->menuindex,
            ));
        }
        $c->prepare();
        $c->stmt->execute();

        $source->set('menuindex', $target->get('menuindex'));
        $source->save();
    }


    /**
     * @param msProduct $source
     */
    public function move(msProduct $source)
    {
        $source->set('parent', $this->_parent);
        $source->set('menuindex', $this->modx->getCount($this->classKey, array('parent' => $this->_parent)));
        $source->save();
    }


    /**
     *
     */
    public function updateIndex()
    {
        $q = $this->modx->newQuery($this->classKey, array('parent' => $this->_parent));
        $q->select('id');
        $q->sortby('menuindex ASC, id', 'ASC');

        if ($q->prepare() && $q->stmt->execute()) {
            $ids = $q->stmt->fetchAll(PDO::FETCH_COLUMN);
            $sql = '';
            $table = $this->modx->getTableName($this->classKey);
            foreach ($ids as $k => $id) {
                $sql .= "UPDATE {$table} SET `menuindex` = '{$k}' WHERE `id` = '{$id}';";
            }
            $this->modx->exec($sql);
        }
    }

}

return 'msProductSortProcessor';