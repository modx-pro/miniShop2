<?php

class msProductSortProcessor extends modObjectProcessor {
	public $objectType = 'msProduct';
	private $parent;

	public function process() {
		/* @var msProduct $source */
		$source = $this->modx->getObject($this->objectType, $this->getProperty('source'));
		/* @var msProduct $target */
		$target = $this->modx->getObject($this->objectType, $this->getProperty('target'));

		if (empty($source) || empty($target)) {
			return $this->modx->error->failure();
		}
		$this->parent = $source->get('parent');

		if ($source->get('menuindex') < $target->get('menuindex')) {
			$this->modx->exec("UPDATE {$this->modx->getTableName($this->objectType)}
				SET menuindex = menuindex - 1 WHERE
					menuindex <= {$target->get('menuindex')}
					AND menuindex > {$source->get('menuindex')}
					AND menuindex > 0
			");

		} else {
			$this->modx->exec("UPDATE {$this->modx->getTableName($this->objectType)}
				SET menuindex = menuindex + 1 WHERE
					menuindex >= {$target->get('menuindex')}
					AND menuindex < {$source->get('menuindex')}
			");
		}
		$newRank = $target->get('menuindex');
		$source->set('menuindex',$newRank);
		$source->save();

		if (!$this->modx->getCount($this->objectType, array('menuindex' => 0, 'parent' => $this->parent))) {
			$this->setIndex();
		}
		return $this->modx->error->success();
	}

	public function setIndex() {
		$q = $this->modx->newQuery($this->objectType, array('parent' => $this->parent));
		$q->select('id');
		$q->sortby('menuindex ASC, id', 'ASC');

		if ($q->prepare() && $q->stmt->execute()) {
			$ids = $q->stmt->fetchAll(PDO::FETCH_COLUMN);
			$sql = '';
			$table = $this->modx->getTableName($this->objectType);
			foreach ($ids as $k => $id) {
				$sql .= "UPDATE {$table} SET `menuindex` = '{$k}' WHERE `id` = '{$id}';";
			}
			$this->modx->exec($sql);
		}
	}
}

return 'msProductSortProcessor';