<?php

class msOrderStatusGetListProcessor extends modObjectGetListProcessor {
	public $classKey = 'msOrderStatus';
	public $defaultSortField = 'rank';
	public $defaultSortDirection  = 'asc';

	public function prepareQueryBeforeCount(xPDOQuery $c) {
		return $c;
	}

	public function prepareRow(xPDOObject $object) {
		$array = $object->toArray();
		return $array;
	}

}

return 'msOrderStatusGetListProcessor';