<?php

class msProductSortProcessor extends modObjectProcessor {
	public $classKey = 'msCategoryMember';
	private $parent;


	/** {@inheritDoc} */
	public function process() {
		/* @var msProduct $source */
		$source = $this->modx->getObject($this->classKey, array(
			'product_id' => $this->getProperty('source')
			,'category_id' => $this->getProperty('parent')
		));
		/* @var msProduct $target */
		$target = $this->modx->getObject($this->classKey, array(
			'product_id' => $this->getProperty('target')
			,'category_id' => $this->getProperty('parent')
		));

		if (empty($source) || empty($target)) {
			return $this->modx->error->failure();
		}

		// get "source" msCategoryMember
		// add 1 to all ranks AFTER AND INCLUDING that source
		// update "target" with new msCategoryMember
		// re-number to fill any gaps
		$newRankForTarget = $target->get('rank');

		$q = "UPDATE {$this->modx->getTableName($this->classKey)}
				SET rank = rank + 1 WHERE
					rank >= {$target->get('rank')}
					AND category_id = {$this->getProperty('parent')}";
		$this->modx->exec($q);

		$q = "UPDATE {$this->modx->getTableName($this->classKey)}
				SET rank = {$newRankForTarget} WHERE
					product_id = {$this->getProperty('source')}
					AND category_id = {$this->getProperty('parent')}";
		$this->modx->exec($q);

		$this->setIndex();

		return $this->modx->error->success();
	}


	/** {@inheritDoc} */
	public function setIndex() {
		$q = $this->modx->newQuery($this->classKey, array('category_id' => $this->getProperty('parent')));
		$q->select('product_id');
		$q->sortby('rank ASC, product_id', 'ASC');

		if ($q->prepare() && $q->stmt->execute()) {
			$ids = $q->stmt->fetchAll(PDO::FETCH_COLUMN);
			$sql = '';
			$table = $this->modx->getTableName($this->classKey);
			$r = 0;
			foreach ($ids as $k => $id) {
				$r++;
				$sql .= "UPDATE {$table} SET `rank` = '{$r}' WHERE `product_id` = '{$id}' AND category_id = '{$this->getProperty('parent')}';";
			}
			$this->modx->exec($sql);
		}
	}

}

return 'msProductSortProcessor';