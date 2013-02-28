<?php

// It is adapted code from https://github.com/splittingred/Gallery/blob/a51442648fde1066cf04d46550a04265b1ad67da/core/components/gallery/processors/mgr/item/sort.php

class msPaymentSortProcessor extends modObjectProcessor {
	public $objectType = 'msPayment';

	public function process() {
		/* @var msPayment $source */
		$source = $this->modx->getObject($this->objectType, $this->getProperty('source'));
		/* @var msPayment $target */
		$target = $this->modx->getObject($this->objectType, $this->getProperty('target'));

		if (empty($source) || empty($target)) {
			return $this->modx->error->failure();
		}

		if ($source->get('rank') < $target->get('rank')) {
			$this->modx->exec("UPDATE {$this->modx->getTableName($this->objectType)}
				SET rank = rank - 1 WHERE
					rank <= {$target->get('rank')}
					AND rank > {$source->get('rank')}
					AND rank > 0
			");

		} else {
			$this->modx->exec("UPDATE {$this->modx->getTableName($this->objectType)}
				SET rank = rank + 1 WHERE
					rank >= {$target->get('rank')}
					AND rank < {$source->get('rank')}
			");
		}
		$newRank = $target->get('rank');
		$source->set('rank',$newRank);
		$source->save();

		if (!$this->modx->getCount($this->objectType, array('rank' => 0))) {
			$this->setRanks();
		}
		return $this->modx->error->success();
	}

	public function setRanks() {
		$q = $this->modx->newQuery($this->objectType);
		$q->select('id');
		$q->sortby('rank ASC, id', 'ASC');

		if ($q->prepare() && $q->stmt->execute()) {
			$ids = $q->stmt->fetchAll(PDO::FETCH_COLUMN);
			$sql = '';
			$table = $this->modx->getTableName($this->objectType);
			foreach ($ids as $k => $id) {
				$sql .= "UPDATE {$table} SET `rank` = '{$k}' WHERE `id` = '{$id}';";
			}
			$this->modx->exec($sql);
		}
	}

}

return 'msPaymentSortProcessor';