<?php

// It is adapted code from https://github.com/splittingred/Gallery/blob/a51442648fde1066cf04d46550a04265b1ad67da/core/components/gallery/processors/mgr/item/sort.php

class msOrderStatusSortProcessor extends modObjectProcessor {

	public function process() {
		/* @var msOrderStatus $source */
		$source = $this->modx->getObject('msOrderStatus', $this->getProperty('source'));
		/* @var msOrderStatus $target */
		$target = $this->modx->getObject('msOrderStatus', $this->getProperty('target'));

		if (empty($source) || empty($target)) {
			return $this->modx->error->failure();
		}

		if ($source->get('rank') < $target->get('rank')) {
			$this->modx->exec("UPDATE {$this->modx->getTableName('msOrderStatus')}
				SET rank = rank - 1 WHERE
					rank <= {$target->get('rank')}
					AND rank > {$source->get('rank')}
					AND rank > 0
			");

		} else {
			$this->modx->exec("UPDATE {$this->modx->getTableName('msOrderStatus')}
				SET rank = rank + 1 WHERE
					rank >= {$target->get('rank')}
					AND rank < {$source->get('rank')}
			");
		}
		$newRank = $target->get('rank');
		$source->set('rank',$newRank);
		$source->save();

		return $this->modx->error->success();
	}
}

return 'msOrderStatusSortProcessor';