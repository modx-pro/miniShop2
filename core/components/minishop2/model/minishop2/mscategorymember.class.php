<?php
class msCategoryMember extends xPDOObject {

	public function save($cacheFlag) {
		$res = parent::save($cacheFlag);
		if($this->get('rank') == 0) {
			$max = 0;
			$q = $this->xpdo->newQuery('msCategoryMember');
			$q->where(array('category_id' => $this->get('category_id')));
			$q->sortby('rank', 'DESC');
			$q->limit(1);
			$maxObj = $this->xpdo->getObject('msCategoryMember', $q);
			if($maxObj) {
				$max = $maxObj->get('rank') + 1;
			}

			$q = "UPDATE {$this->xpdo->getTableName('msCategoryMember')}
				SET rank = {$max} WHERE
					product_id = {$this->get('product_id')}
					AND category_id = {$this->get('category_id')}";
			$this->xpdo->exec($q);
		}
		return $res;
	}
}