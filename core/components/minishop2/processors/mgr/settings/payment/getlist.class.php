<?php

class msPaymentGetListProcessor extends modObjectGetListProcessor {
	public $classKey = 'msPayment';
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

return 'msPaymentGetListProcessor';