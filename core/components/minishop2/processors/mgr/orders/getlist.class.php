<?php
/**
 * Get a list of Orders
 *
 * @package minishop2
 * @subpackage processors
 */
class msOrderGetListProcessor extends modObjectGetListProcessor {
	public $classKey = 'msOrder';
	public $defaultSortField = 'id';
	public $defaultSortDirection  = 'DESC';
	public $languageTopics = array('default','minishop2:manager');


	public function prepareQueryBeforeCount(xPDOQuery $c) {
		$c->leftJoin('modUserProfile','modUserProfile', '`msOrder`.`user_id` = `modUserProfile`.`internalKey`');
		$c->leftJoin('msOrderStatus','msOrderStatus', '`msOrder`.`status` = `msOrderStatus`.`id`');
		$c->leftJoin('msDelivery','msDelivery', '`msOrder`.`delivery` = `msDelivery`.`id`');
		$c->leftJoin('msPayment','msPayment', '`msOrder`.`payment` = `msPayment`.`id`');

		$orderColumns = $this->modx->getSelectColumns('msOrder', 'msOrder', '', array('status','delivery','payment'), true);
		$c->select($orderColumns . ', `modUserProfile`.`fullname` as `customer`, `msOrderStatus`.`name` as `status`, `msOrderStatus`.`color`, `msDelivery`.`name` as `delivery`, `msPayment`.`name` as `payment`');

		if ($query = $this->getProperty('query')) {
			$c->where(array(
				'num:LIKE' => '%'.$query.'%'
				,'OR:comment:LIKE' => '%'.$query.'%'
				,'OR:modUserProfile.fullname:LIKE' => '%'.$query.'%'
			));
		}
		if ($status = $this->getProperty('status')) {
			$c->where(array('status' => $status));
		}

		return $c;
	}

	public function getData() {
		$data = array();
		$limit = intval($this->getProperty('limit'));
		$start = intval($this->getProperty('start'));

		/* query for chunks */
		$c = $this->modx->newQuery($this->classKey);
		$c = $this->prepareQueryBeforeCount($c);
		$data['total'] = $this->modx->getCount($this->classKey,$c);
		$c = $this->prepareQueryAfterCount($c);

		$sortClassKey = $this->getSortClassKey();
		$sortKey = $this->modx->getSelectColumns($sortClassKey,$this->getProperty('sortAlias',$sortClassKey),'',array($this->getProperty('sort')));
		if (empty($sortKey)) $sortKey = $this->getProperty('sort');
		$c->sortby($sortKey,$this->getProperty('dir'));
		if ($limit > 0) {
			$c->limit($limit,$start);
		}

		if ($c->prepare() && $c->stmt->execute()) {
			$data['results'] = $c->stmt->fetchAll(PDO::FETCH_ASSOC);
		}

		return $data;
	}

	public function iterate(array $data) {
		$list = array();
		$list = $this->beforeIteration($list);
		$this->currentIndex = 0;
		/** @var xPDOObject|modAccessibleObject $object */
		foreach ($data['results'] as $array) {
			$list[] = $this->prepareArray($array);
			$this->currentIndex++;
		}
		$list = $this->afterIteration($list);
		return $list;
	}

	public function prepareArray(array $data) {
		$data['status'] = '<span style="color:#'.$data['color'].';">'.$data['status'].'</span>';
		return $data;
	}


}

return 'msOrderGetListProcessor';