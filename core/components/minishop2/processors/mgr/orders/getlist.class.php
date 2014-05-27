<?php

class msOrderGetListProcessor extends modObjectGetListProcessor {
	public $classKey = 'msOrder';
	public $languageTopics = array('default','minishop2:manager');
	public $defaultSortField = 'id';
	public $defaultSortDirection  = 'DESC';
	public $permission = 'msorder_list';
	/** @var  miniShop2 $ms2 */
	protected $ms2;

	/** {@inheritDoc} */
	public function initialize() {
		$this->ms2 = $this->modx->getService('miniShop2');

		if (!$this->modx->hasPermission($this->permission)) {
			return $this->modx->lexicon('access_denied');
		}
		return parent::initialize();
	}


	/** {@inheritDoc} */
	public function prepareQueryBeforeCount(xPDOQuery $c) {
		$c->leftJoin('modUser','modUser', '`msOrder`.`user_id` = `modUser`.`id`');
		$c->leftJoin('modUserProfile','modUserProfile', '`msOrder`.`user_id` = `modUserProfile`.`internalKey`');
		$c->leftJoin('msOrderStatus','msOrderStatus', '`msOrder`.`status` = `msOrderStatus`.`id`');
		$c->leftJoin('msDelivery','msDelivery', '`msOrder`.`delivery` = `msDelivery`.`id`');
		$c->leftJoin('msPayment','msPayment', '`msOrder`.`payment` = `msPayment`.`id`');

		$orderColumns = $this->modx->getSelectColumns('msOrder', 'msOrder', '', array('status','delivery','payment'), true);
		$c->select($orderColumns . ', `modUserProfile`.`fullname` as `customer`, `modUser`.`username` as `customer_username`, `msOrderStatus`.`name` as `status`, `msOrderStatus`.`color`, `msDelivery`.`name` as `delivery`, `msPayment`.`name` as `payment`');

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


	/** {@inheritDoc} */
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


	/** {@inheritDoc} */
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


	/** {@inheritDoc} */
	public function prepareArray(array $data) {
		if (empty($data['customer'])) {
			$data['customer'] = $data['customer_username'];
		}
		
		$data['status'] = '<span style="color:#'.$data['color'].';">'.$data['status'].'</span>';

		$data['actions'] = array(
			array(
				'className' => 'update',
				'text' => $this->modx->lexicon('ms2_menu_update'),
			),
			array(
				'className' => 'delete',
				'text' => $this->modx->lexicon('ms2_menu_remove'),
			),
		);

		if (isset($data['cost'])) {$data['cost'] = $this->ms2->formatPrice($data['cost']);}
		if (isset($data['cart_cost'])) {$data['cart_cost'] = $this->ms2->formatPrice($data['cart_cost']);}
		if (isset($data['delivery_cost'])) {$data['delivery_cost'] = $this->ms2->formatPrice($data['delivery_cost']);}
		if (isset($data['weight'])) {$data['weight'] = $this->ms2->formatWeight($data['weight']);}

		return $data;
	}


}

return 'msOrderGetListProcessor';
