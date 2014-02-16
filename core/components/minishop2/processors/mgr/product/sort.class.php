<?php

class msProductSortProcessor extends modObjectProcessor {
	public $classKey = 'msProduct';
	private $parent;


	/** {@inheritDoc} */
	public function process() {
		/* @var msProduct $source */
		$source = $this->modx->getObject($this->classKey, $this->getProperty('source'));
		/* @var msProduct $target */
		$target = $this->modx->getObject($this->classKey, $this->getProperty('target'));

		if (empty($source) || empty($target)) {
			return $this->modx->error->failure();
		}
		$this->parent = $source->get('parent');

		if ($source->get('menuindex') < $target->get('menuindex')) {
			$this->modx->exec("UPDATE {$this->modx->getTableName($this->classKey)}
				SET menuindex = menuindex - 1 WHERE
					menuindex <= {$target->get('menuindex')}
					AND menuindex > {$source->get('menuindex')}
					AND menuindex > 0
			");

		} else {
			$this->modx->exec("UPDATE {$this->modx->getTableName($this->classKey)}
				SET menuindex = menuindex + 1 WHERE
					menuindex >= {$target->get('menuindex')}
					AND menuindex < {$source->get('menuindex')}
			");
		}
		$newRank = $target->get('menuindex');
		$source->set('menuindex',$newRank);
		$source->save();

		if (!$this->modx->getCount($this->classKey, array('menuindex' => 0, 'parent' => $this->parent))) {
			$this->setIndex();
		}
		return $this->modx->error->success();
	}


	/** {@inheritDoc} */
	public function setIndex() {
		$q = $this->modx->newQuery($this->classKey, array('parent' => $this->parent));
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