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
        if (!$target = $this->modx->getObject($this->classKey, ['id' => $this->getProperty('target')])) {
            return $this->failure();
        }
        $this->_parent = $target->get('parent');

        $sources = json_decode($this->getProperty('sources'), true);
        if (!is_array($sources)) {
            return $this->failure();
        }
        foreach ($sources as $id) {
            /** @var msProduct $source */
            $source = $this->modx->getObject($this->classKey, compact('id'));
            if ($source->get('parent') == $this->_parent) {
                $target = $this->modx->getObject($this->classKey, ['id' => $this->getProperty('target')]);
                $this->sort($source, $target);
            } else {
                $this->move($source);
            }
        }
        $this->updateIndex();

        return $this->modx->error->success();
    }

    /**
     * @param msProduct $source
     * @param msProduct $target
     */
    public function sort(msProduct $source, msProduct $target)
    {
        $c = $this->modx->newQuery($this->classKey);
        $c->command('UPDATE');
        $c->where([
            'parent' => $this->_parent,
        ]);
        if ($source->get('menuindex') < $target->get('menuindex')) {
            $c->query['set']['menuindex'] = [
                'value' => '`menuindex` - 1',
                'type' => false,
            ];
            $c->andCondition([
                'menuindex:<=' => $target->menuindex,
                'menuindex:>' => $source->menuindex,
            ]);
            $c->andCondition([
                'menuindex:>' => 0,
            ]);
        } else {
            $c->query['set']['menuindex'] = [
                'value' => '`menuindex` + 1',
                'type' => false,
            ];
            $c->andCondition([
                'menuindex:>=' => $target->menuindex,
                'menuindex:<' => $source->menuindex,
            ]);
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
        $source->set('menuindex', $this->modx->getCount($this->classKey, ['parent' => $this->_parent]));
        $source->save();
    }

    /**
     *
     */
    public function updateIndex()
    {
        // Check if need to update children indexes
        $c = $this->modx->newQuery($this->classKey, ['parent' => $this->_parent]);
        $c->groupby('menuindex');
        $c->select('COUNT(menuindex) as idx');
        $c->sortby('idx', 'DESC');
        $c->limit(1);
        if ($c->prepare() && $c->stmt->execute()) {
            if ($c->stmt->fetchColumn() == 1) {
                return;
            }
        }

        // Update indexes
        $c = $this->modx->newQuery($this->classKey, ['parent' => $this->_parent]);
        $c->select('id');
        $c->sortby('menuindex ASC, id', 'ASC');
        if ($c->prepare() && $c->stmt->execute()) {
            $table = $this->modx->getTableName($this->classKey);
            $update = $this->modx->prepare("UPDATE {$table} SET menuindex = ? WHERE id = ?");
            $i = 0;
            while ($id = $c->stmt->fetch(PDO::FETCH_COLUMN)) {
                $update->execute([$i, $id]);
                $i++;
            }
        }
    }
}

return 'msProductSortProcessor';
