<?php

class msDeliveryGetListProcessor extends modObjectGetListProcessor {
	public $classKey = 'msDelivery';
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

return 'msDeliveryGetListProcessor';