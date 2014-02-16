<?php

class msLinkGetListProcessor extends modObjectGetListProcessor {
	public $classKey = 'msLink';
	public $defaultSortField = 'id';
	public $defaultSortDirection  = 'ASC';
	public $permission = 'mssetting_list';


	/** {@inheritDoc} */
	public function initialize() {
		if (!$this->modx->hasPermission($this->permission)) {
			return $this->modx->lexicon('access_denied');
		}
		return parent::initialize();
	}


	/** {@inheritDoc} */
	public function process() {
		$beforeQuery = $this->beforeQuery();
		if ($beforeQuery !== true) {
			return $this->failure($beforeQuery);
		}
		$data = $this->getData();
		$list = $this->iterate($data);
		return $this->outputArray($list,$data['total']);
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
			$objectArray = $this->prepareResult($array);
			if (!empty($objectArray) && is_array($objectArray)) {
				$list[] = $objectArray;
				$this->currentIndex++;
			}
		}
		$list = $this->afterIteration($list);
		return $list;
	}


	/** {@inheritDoc} */
	public function prepareQueryBeforeCount(xPDOQuery $c) {
		if ($this->getProperty('combo')) {
			$c->select('id,name');
		}
		else {
			$c->select($this->modx->getSelectColumns('msLink'));
		}
		if ($query = $this->getProperty('query')) {
			$c->where(array('name:LIKE' => "%$query%", 'OR:type:LIKE' => "%$query%"));
		}

		return $c;
	}


	/** {@inheritDoc} */
	public function prepareResult(array $array) {
		return $array;
	}

}

return 'msLinkGetListProcessor';