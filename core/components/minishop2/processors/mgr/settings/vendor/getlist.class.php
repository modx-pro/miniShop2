<?php

class msVendorGetListProcessor extends modObjectGetListProcessor {
	public $classKey = 'msVendor';
	public $defaultSortField = 'id';
	public $defaultSortDirection  = 'ASC';
	public $item_id = 0;
	public $permission = 'mssetting_list';

	/**
	 * {@inheritDoc}
	 * @return boolean
	 */
	public function initialize() {
		if (!$this->modx->hasPermission($this->permission)) {
			return $this->modx->lexicon('access_denied');
		}
		if ($this->getProperty('combo') && !$this->getProperty('limit') && $id = $this->getProperty('id')) {
			$this->item_id = $id;
		}
		$this->setDefaultProperties(array(
			'start' => 0,
			'limit' => 20,
			'sort' => $this->defaultSortField,
			'dir' => $this->defaultSortDirection,
			'combo' => false,
			'query' => '',
		));
		return true;
	}

	/**
	 * {@inheritDoc}
	 * @return mixed
	 */
	public function process() {
		$beforeQuery = $this->beforeQuery();
		if ($beforeQuery !== true) {
			return $this->failure($beforeQuery);
		}
		$data = $this->getData();
		$list = $this->iterate($data);
		return $this->outputArray($list,$data['total']);
	}


	/**
	 * Get the data of the query
	 * @return array
	 */
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


	/**
	 * Iterate across the data
	 *
	 * @param array $data
	 * @return array
	 */
	public function iterate(array $data) {
		$list = array();
		$list = $this->beforeIteration($list);
		$this->currentIndex = 0;
		/** @var xPDOObject|modAccessibleObject $object */
		foreach ($data['results'] as $array) {
			$objectArray = $this->prepareResult($array);
			if (!empty($objectArray) && is_array($objectArray)) {
				$list[] = $objectArray;
				$this->currentIndex++;
			}
		}
		$list = $this->afterIteration($list);
		return $list;
	}

	/**
	 * {@inheritDoc}
	 * @return xPDOQuery
	 */
	public function prepareQueryBeforeCount(xPDOQuery $c) {
		if ($this->getProperty('combo')) {
			$c->select('id,name');
		}
		else {
			$c->select($this->modx->getSelectColumns('msVendor'));
		}
		if ($query = $this->getProperty('query')) {
			$c->where(array('name:LIKE' => "%$query%"));
		}
		elseif ($this->item_id) {
			$c->where(array('id' => $this->item_id));
		}

		return $c;
	}

	/**
	 * {@inheritDoc}
	 * @return array
	 */
	public function prepareResult(array $array) {
		return $array;
	}
}

return 'msVendorGetListProcessor';