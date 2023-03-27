<?php

class msPaymentSortProcessor extends modObjectProcessor
{
    public $classKey = 'msPayment';
    public $permission = 'mssetting_save';

    /**
     * @return bool|null|string
     */
    public function initialize()
    {
        if (!$this->modx->hasPermission($this->permission)) {
            return $this->modx->lexicon('access_denied');
        }

        return parent::initialize();
    }

    /**
     * @return array|string
     */
    public function process()
    {
        if (!$this->modx->getCount($this->classKey, $this->getProperty('target'))) {
            return $this->failure();
        }

        $sources = json_decode($this->getProperty('sources'), true);
        if (!is_array($sources)) {
            return $this->failure();
        }
        foreach ($sources as $id) {
            /** @var msPayment $source */
            $source = $this->modx->getObject($this->classKey, compact('id'));
            /** @var msPayment $target */
            $target = $this->modx->getObject($this->classKey, ['id' => $this->getProperty('target')]);
            $this->sort($source, $target);
        }
        $this->updateIndex();

        return $this->modx->error->success();
    }

    /**
     * @param msPayment $source
     * @param msPayment $target
     *
     * @return array|string
     */
    public function sort(msPayment $source, msPayment $target)
    {
        $c = $this->modx->newQuery($this->classKey);
        $c->command('UPDATE');
        if ($source->get('rank') < $target->get('rank')) {
            $c->query['set']['menuindex'] = [
                'value' => '`menuindex` - 1',
                'type' => false,
            ];
            $c->andCondition([
                'rank:<=' => $target->rank,
                'rank:>' => $source->rank,
            ]);
            $c->andCondition([
                'rank:>' => 0,
            ]);
        } else {
            $c->query['set']['rank'] = [
                'value' => '`rank` + 1',
                'type' => false,
            ];
            $c->andCondition([
                'rank:>=' => $target->rank,
                'rank:<' => $source->rank,
            ]);
        }
        $c->prepare();
        $c->stmt->execute();

        $source->set('rank', $target->rank);
        $source->save();
    }

    /**
     *
     */
    public function updateIndex()
    {
        // Check if need to update indexes
        $c = $this->modx->newQuery($this->classKey);
        $c->groupby('rank');
        $c->select('COUNT(rank) as idx');
        $c->sortby('idx', 'DESC');
        $c->limit(1);
        if ($c->prepare() && $c->stmt->execute()) {
            if ($c->stmt->fetchColumn() == 1) {
                return;
            }
        }

        // Update indexes
        $c = $this->modx->newQuery($this->classKey);
        $c->select('id');
        $c->sortby('rank ASC, id', 'ASC');
        if ($c->prepare() && $c->stmt->execute()) {
            $table = $this->modx->getTableName($this->classKey);
            $update = $this->modx->prepare("UPDATE {$table} SET rank = ? WHERE id = ?");
            $i = 0;
            while ($id = $c->stmt->fetch(PDO::FETCH_COLUMN)) {
                $update->execute([$i, $id]);
                $i++;
            }
        }
    }
}

return 'msPaymentSortProcessor';
